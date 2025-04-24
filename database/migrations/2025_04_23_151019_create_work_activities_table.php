<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_activities', function (Blueprint $table) {
            $table->id();

            // Empleado (llave foránea)
            $table->foreignId('employee_id')
            ->constrained('employees', 'id')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            // Día trabajado (llave foránea)
            $table->foreignId('work_day_id')
            ->constrained('work_days', 'id')
            ->cascadeOnDelete();

            $table->string('title'); // Ej: "Reunión con equipo"
            $table->string('subtitle'); // Ej: "Sobre los avances de ayer"
            $table->text('description'); // Detalles
            $table->time('start_time')->nullable(); // Hora de inicio
            $table->time('end_time')->nullable(); // Hora de fin
            $table->integer('duration_hours')->default(0); // Duración (puede calcularse automáticamente)
            $table->text('notes'); // Notas adicionales
            $table->string('tags')->nullable(); // Etiquetas
            $table->enum('status',
            [
                'pending', // Pendiente
                'approved', // Aprobado
                'rejected', // Rechazado
                'paid', // Pagado
                'unpaid' // No Pagado
            ])->default('pending'); // estado

            // Crear indices
            // Más rápido para: SELECT con WHERE, JOIN, ORDER BY
            // Más lento para: INSERT, UPDATE, DELETE en esas columnas
            // (aunque la diferencia suele ser pequeña)
            $table->index('employee_id');
            $table->index('work_day_id');
            $table->index('start_time');
            $table->index('end_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_activities');
    }
};
