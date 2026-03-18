<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('direccion_id')->constrained('direcciones');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos');
            $table->enum('tipo_vehiculo', ['unico', 'proximo'])->default('unico');
            $table->enum('condicion', [
                'aire', 'baul', 'mascota', 'parrilla', 'transferencia',
                'daviplata', 'polarizados', 'silla_ruedas', 'ninguno'
            ])->default('ninguno');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'asignado', 'en_camino', 'finalizado', 'cancelado'])->default('pendiente');
            $table->dateTime('fecha_solicitud')->useCurrent();
            $table->dateTime('fecha_asignacion')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->foreignId('operador_id')->constrained('usuarios');
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            $table->index('estado');
            $table->index('fecha_solicitud');
            $table->index('fecha_actualizacion');
        });

        Schema::create('historial_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('estado_anterior', ['pendiente', 'asignado', 'en_camino', 'finalizado', 'cancelado'])->nullable();
            $table->enum('estado_nuevo', ['pendiente', 'asignado', 'en_camino', 'finalizado', 'cancelado']);
            $table->dateTime('fecha_cambio')->useCurrent();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnUpdate();

            $table->index('fecha_cambio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_servicios');
        Schema::dropIfExists('servicios');
    }
};
