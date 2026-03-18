<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('telefono', 15)->unique();
            $table->string('nombre', 100)->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('direccion', 255);
            $table->string('direccion_normalizada', 255);
            $table->string('referencia', 255)->nullable();
            $table->boolean('es_frecuente')->default(false);
            $table->boolean('activa')->default(true);
            $table->dateTime('ultimo_uso');
            $table->dateTime('fecha_registro');

            $table->index('direccion_normalizada');
            $table->index('ultimo_uso');
        });

        Schema::create('patrones_direccion', function (Blueprint $table) {
            $table->id();
            $table->string('patron', 100)->unique();
            $table->string('reemplazo', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrones_direccion');
        Schema::dropIfExists('direcciones');
        Schema::dropIfExists('clientes');
    }
};
