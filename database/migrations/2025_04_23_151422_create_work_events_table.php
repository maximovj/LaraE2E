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
        Schema::create('work_events', function (Blueprint $table) {
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

            // Actividades del trabajo (llave foránea)
            $table->foreignId('work_activity_id')
            ->constrained('work_activities', 'id')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->string('title');
            $table->boolean('allDay')->default(false);
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->text('url')->nullable();
            $table->text('classnames')->nullable();
            $table->string('backgroundColor')->default('');
            $table->string('borderColor')->default('');
            $table->string('textColor')->default('');
            $table->boolean('overlap')->default(true);
            $table->boolean('editable')->default(true);
            $table->boolean('startEditable')->default(true);
            $table->boolean('durationEditable')->default(true);
            $table->boolean('display')->default(true);

            // Crear indices
            // Más rápido para: SELECT con WHERE, JOIN, ORDER BY
            // Más lento para: INSERT, UPDATE, DELETE en esas columnas
            // (aunque la diferencia suele ser pequeña)
            $table->index('employee_id');
            $table->index('work_day_id');
            $table->index('work_activity_id');
            $table->index('start');
            $table->index('end');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_events');
    }
};
