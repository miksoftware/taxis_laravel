<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos_sancion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->text('descripcion');
            $table->integer('tiempo_sancion')->comment('Tiempo en minutos');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamp('fecha_registro')->useCurrent();
        });

        Schema::create('sanciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('articulo_id')->constrained('articulos_sancion');
            $table->foreignId('usuario_id')->constrained('usuarios')->comment('Usuario que aplica');
            $table->text('motivo')->nullable();
            $table->dateTime('fecha_inicio')->useCurrent();
            $table->dateTime('fecha_fin');
            $table->enum('estado', ['activa', 'cumplida', 'anulada'])->default('activa');
            $table->timestamp('fecha_registro')->useCurrent();
        });

        Schema::create('historial_sanciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sancion_id')->constrained('sanciones')->cascadeOnDelete();
            $table->enum('accion', ['aplicada', 'anulada', 'cumplida', 'modificada']);
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->text('comentario')->nullable();
            $table->dateTime('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_sanciones');
        Schema::dropIfExists('sanciones');
        Schema::dropIfExists('articulos_sancion');
    }
};
