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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();

            // Llave foránea
            $table->foreignId('company_id')
            ->nullable()
            ->constrained('companies', 'id')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            // manager_id // TODO: Agregar encargado de la tabla de empleados

            $table->string('name');  // "Sucursal Centro"
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('address');
            $table->string('phone');
            $table->string('code_office');
            $table->string('zip_code');

            $table->enum('status', [
                'active', // activo
                'inactive', // inactivo
                'closed', // cerrado
                'open',  // abierto
                'available', // disponible
                'unavailable' // no disponible
            ]);

            $table->enum('type', [
                'Corporate office', //  Corporativo
                'Branch office', // Sucursal
                'Warehouse', // Almacén
                'Head office', // Oficina principal / Matriz / Main office
                'Regional office', // Oficina regional
                'Distribution center', // Centro de distribución
                'Administrative office', // Oficina administrativa
                'Remote office', // Oficina remota
                'Sales office', // Oficina de ventas
                'Satellite office', // Oficina satélite
                'Customer service center' // Centro de atención al cliente
            ]);

            $table->point('coordinates')->nullable();

            $table->json('business_hours')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
