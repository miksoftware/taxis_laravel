<?php

namespace App\Jobs;

use App\Models\Sancion;
use App\Models\HistorialSancion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificarSancionesVencidas implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $sancionesVencidas = Sancion::vencidas()
            ->with('vehiculo')
            ->get();

        if ($sancionesVencidas->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($sancionesVencidas) {
            foreach ($sancionesVencidas as $sancion) {
                // Marcar sanción como cumplida
                $sancion->update(['estado' => 'cumplida']);

                // Liberar vehículo
                $sancion->vehiculo->update(['estado' => 'disponible']);

                // Registrar en historial
                HistorialSancion::create([
                    'sancion_id' => $sancion->id,
                    'accion' => 'cumplida',
                    'usuario_id' => 1, // Sistema (superadmin)
                    'comentario' => 'Sanción cumplida automáticamente por el sistema.',
                    'fecha' => now(),
                ]);
            }
        });

        Log::info("Sanciones verificadas: {$sancionesVencidas->count()} cumplidas automáticamente.");
    }
}
