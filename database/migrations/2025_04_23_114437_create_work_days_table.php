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
        Schema::create('work_days', function (Blueprint $table) {
            $table->id();

            // Empleado (llave foránea)
            $table->foreignId('employee_id')
            ->constrained('employees', 'id')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->date('date'); // Fecha del día
            $table->decimal('hourly_rate', 10, 2)->default(0.0);
            $table->integer('total_events')->default(0); // Total de eventos
            $table->decimal('total_hours', 8, 2)->default(0.0); // Total de horas
            $table->decimal('amount', 10, 2)->default(0.0); // Monto a pagar
            $table->boolean('billable')->default(false); // es pagable
            $table->enum('status',
            [
                'pending',
                'approved',
                'rejected',
                'paid',
                'unpaid'
            ])->default('pending'); // estado
            $table->text('details')->nullable(); // detalles
            $table->text('note')->nullable(); // notas
            $table->json('tags')->nullable();

            // Índice único compuesto (combinación única de ambos campos)
            $table->unique(['employee_id', 'date']);

            // Crear indices
            // Más rápido para: SELECT con WHERE, JOIN, ORDER BY
            // Más lento para: INSERT, UPDATE, DELETE en esas columnas
            // (aunque la diferencia suele ser pequeña)
            $table->index('date');
            $table->index('employee_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_days');
    }
};
