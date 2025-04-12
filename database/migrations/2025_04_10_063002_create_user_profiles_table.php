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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            // Llave foránea asociada a un usuario
            $table
            ->foreignId('user_id')
            ->nullable()
            ->constrained('users', 'id')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->string('profile_picture')->nullable();
            $table->string('first_names');
            $table->string('last_names');
            $table->tinyInteger('age')->default(0);
            $table->date('birthdate');
            $table->string('blood_type', 10);
            $table->string('address');
            $table->string('zip_code');
            $table->string('ssn')->unique();
            $table->string('bank');
            $table->string('interbank_clabe')->unique();
            $table->text('notes')->nullable();

            $table->enum('marital_status', [
                'single', // soltero
                'married', // casado
                'divorced', // divorciado
                'widowed', // viudo
                'separated', // separado
                'engaged', // comprometido
                'domestic_partnership', // unión libre
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
