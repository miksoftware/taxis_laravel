<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupImportController extends Controller
{
    private array $tablas = [
        'usuarios', 'clientes', 'patrones_direccion', 'direcciones',
        'vehiculos', 'articulos_sancion', 'sanciones', 'historial_sanciones',
        'servicios', 'historial_servicios',
    ];

    public function index()
    {
        return view('backup.index');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|max:102400',
        ], [
            'sql_file.required' => 'Debe seleccionar un archivo SQL.',
            'sql_file.file'     => 'El archivo no es válido.',
            'sql_file.max'      => 'El archivo no puede superar 100MB.',
        ]);

        $file = $request->file('sql_file');

        if (strtolower($file->getClientOriginalExtension()) !== 'sql') {
            return back()->with('error', 'Solo se permiten archivos .sql');
        }

        $contenido = file_get_contents($file->getRealPath());

        if (empty(trim($contenido))) {
            return back()->with('error', 'El archivo SQL está vacío.');
        }

        $resultados = [];
        $errores = [];

        // Guardar superadmin protegido
        $superadmin = DB::table('usuarios')->where('es_protegido', true)->first();

        try {
            // Modo permisivo para evitar truncation errors en ENUMs
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement("SET SESSION sql_mode=''");

            // Fase 1: Limpiar tablas
            foreach (array_reverse($this->tablas) as $tabla) {
                DB::table($tabla)->delete();
                try {
                    DB::statement("ALTER TABLE `{$tabla}` AUTO_INCREMENT = 1");
                } catch (\Exception $e) {}
            }

            // Fase 2: Extraer e importar sentencias del SQL completo
            $sentencias = $this->parsearSentencias($contenido);

            foreach ($this->tablas as $tabla) {
                $insertsTabla = $this->filtrarInserts($sentencias, $tabla);

                if (empty($insertsTabla)) {
                    $resultados[$tabla] = ['importados' => 0, 'estado' => 'sin_datos'];
                    continue;
                }

                $totalImportados = 0;

                foreach ($insertsTabla as $sql) {
                    try {
                        $sql = $this->adaptarSQL($sql, $tabla);
                        if (!empty($sql)) {
                            DB::unprepared($sql);
                            $totalImportados += substr_count($sql, '),(') + 1;
                        }
                    } catch (\Exception $e) {
                        $errores[] = "[{$tabla}] " . mb_substr($e->getMessage(), 0, 200);
                        Log::warning("Backup import [{$tabla}]: " . $e->getMessage());
                    }
                }

                $resultados[$tabla] = ['importados' => $totalImportados, 'estado' => 'ok'];
            }

            // Fase 3: Restaurar superadmin
            if ($superadmin) {
                $existe = DB::table('usuarios')->where('id', $superadmin->id)->exists();
                if (!$existe) {
                    DB::table('usuarios')->insert((array) $superadmin);
                } else {
                    DB::table('usuarios')->where('id', $superadmin->id)
                        ->update(['es_protegido' => true, 'rol' => 'superadmin']);
                }
            }

            // Fase 4: Ajustar auto_increment
            foreach ($this->tablas as $tabla) {
                $this->ajustarAutoIncrement($tabla);
            }

            // Restaurar modos
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::statement("SET SESSION sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

            return back()->with('success', 'Importación completada exitosamente.')
                         ->with('resultados', $resultados)
                         ->with('errores', $errores);

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::statement("SET SESSION sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            Log::error("Backup import falló: " . $e->getMessage());

            if ($superadmin) {
                try {
                    if (!DB::table('usuarios')->where('id', $superadmin->id)->exists()) {
                        DB::table('usuarios')->insert((array) $superadmin);
                    }
                } catch (\Exception $ex) {}
            }

            return back()->with('error', 'Error durante la importación: ' . $e->getMessage());
        }
    }

    /**
     * Parsea el contenido SQL en sentencias individuales.
     * Respeta strings con comillas simples (incluyendo escapes \' y '')
     * para no cortar en ; que estén dentro de datos.
     */
    private function parsearSentencias(string $contenido): array
    {
        // Limpiar referencia a DB vieja
        $contenido = preg_replace('/`taxisdiamantes`\./', '', $contenido);

        $sentencias = [];
        $actual = '';
        $enString = false;
        $len = strlen($contenido);

        for ($i = 0; $i < $len; $i++) {
            $char = $contenido[$i];

            if ($enString) {
                $actual .= $char;
                // Escapar \' o ''
                if ($char === '\\' && $i + 1 < $len) {
                    $actual .= $contenido[++$i];
                    continue;
                }
                if ($char === "'") {
                    // Verificar si es '' (escape de comilla)
                    if ($i + 1 < $len && $contenido[$i + 1] === "'") {
                        $actual .= $contenido[++$i];
                        continue;
                    }
                    $enString = false;
                }
                continue;
            }

            // Fuera de string
            if ($char === "'") {
                $enString = true;
                $actual .= $char;
                continue;
            }

            // Ignorar comentarios de línea
            if ($char === '-' && $i + 1 < $len && $contenido[$i + 1] === '-') {
                // Saltar hasta fin de línea
                while ($i < $len && $contenido[$i] !== "\n") {
                    $i++;
                }
                continue;
            }

            // Ignorar comentarios /* */
            if ($char === '/' && $i + 1 < $len && $contenido[$i + 1] === '*') {
                // Pero permitir /*! ... */ (MySQL conditional comments)
                if ($i + 2 < $len && $contenido[$i + 2] === '!') {
                    // Saltar el comentario condicional completo
                    while ($i < $len) {
                        if ($contenido[$i] === '*' && $i + 1 < $len && $contenido[$i + 1] === '/') {
                            $i += 1;
                            break;
                        }
                        $i++;
                    }
                    continue;
                }
                while ($i < $len) {
                    if ($contenido[$i] === '*' && $i + 1 < $len && $contenido[$i + 1] === '/') {
                        $i += 1;
                        break;
                    }
                    $i++;
                }
                continue;
            }

            if ($char === ';') {
                $trimmed = trim($actual);
                if (!empty($trimmed)) {
                    $sentencias[] = $trimmed . ';';
                }
                $actual = '';
                continue;
            }

            $actual .= $char;
        }

        return $sentencias;
    }

    /**
     * Filtra sentencias INSERT para una tabla específica
     */
    private function filtrarInserts(array $sentencias, string $tabla): array
    {
        $resultado = [];
        foreach ($sentencias as $sql) {
            if (preg_match('/^INSERT\s+INTO\s+`?' . preg_quote($tabla, '/') . '`?\s/i', $sql)) {
                $resultado[] = $sql;
            }
        }
        return $resultado;
    }

    private function adaptarSQL(string $sql, string $tabla): string
    {
        if ($tabla !== 'usuarios') {
            return $sql;
        }

        // INSERT con columnas explícitas
        if (preg_match('/INSERT\s+INTO\s+`?usuarios`?\s*\([^)]+\)\s*VALUES/i', $sql)) {
            $sql = str_replace('`fecha_registro`', '`created_at`', $sql);
            return $sql;
        }

        // INSERT sin columnas
        if (preg_match('/INSERT\s+INTO\s+`?usuarios`?\s+VALUES/i', $sql)) {
            $sql = preg_replace(
                '/INSERT\s+INTO\s+`?usuarios`?\s+VALUES/i',
                'INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `username`, `password`, `telefono`, `rol`, `created_at`, `ultimo_acceso`, `estado`, `token_recuperacion`, `fecha_token`) VALUES',
                $sql
            );
        }

        return $sql;
    }

    private function ajustarAutoIncrement(string $tabla): void
    {
        try {
            $maxId = DB::table($tabla)->max('id');
            if ($maxId) {
                DB::statement("ALTER TABLE `{$tabla}` AUTO_INCREMENT = " . ($maxId + 1));
            }
        } catch (\Exception $e) {}
    }
}
