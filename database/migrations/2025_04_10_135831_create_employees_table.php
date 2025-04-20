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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Llave foránea
            $table
            ->foreignId('user_id')
            ->nullable()
            ->constrained('users', 'id')
            ->onDelete('set null')
            ->onUpdate('cascade'); // Solo si tiene acceso al sistema

            $table
            ->foreignId('company_id')
            ->nullable()
            ->constrained('companies', 'id')
            ->onDelete('cascade')
            ->onUpdate('cascade'); // Obligatorio

            $table
            ->foreignId('office_id')
            ->nullable()
            ->constrained('offices', 'id')
            ->onDelete('cascade')
            ->onUpdate('cascade'); // Obligatorio

            // Llave foránea
            $table
            ->foreignId('manager_id')
            ->nullable()
            ->constrained('users', 'id')
            ->onDelete('set null')
            ->onUpdate('cascade'); // Jefe directo (opcional)

            // Datos laborales
            $table->string('employee_number')->unique(); // "OX-7890"
            $table->string('job_title'); // "Ingeniero de software", "Diseñador"
            $table->string('position'); // "Cajero", "Gerente de Sucursal"
            $table->date('hired_at'); // Fecha_contratación
            $table->enum('status', ['active', 'on_leave', 'fired']); // Estado
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('shift'); // Turno laboral (Ej. "9 AM - 6 PM").
            $table->string('emergency_contact');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
