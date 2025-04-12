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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name')->unique(); // "Oxxo S.A. de C.V."
            $table->string('tax_id')->unique();     // RFC
            $table->string('commercial_name')->unique(); // "Oxxo"
            $table->string('corporate_email_domain')->unique(); // "@oxxo.com.mx"
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
