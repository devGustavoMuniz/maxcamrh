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
        Schema::create('collaborators', function (Blueprint $table) {
          $table->uuid('id')->primary();
          $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
          $table->foreignUuid('client_id')->constrained('clients')->onDelete('cascade');

          $table->string('photo_url')->nullable();
          $table->string('curriculum_url')->nullable();
          $table->date('date_of_birth')->nullable();
          $table->string('gender')->nullable();
          $table->boolean('is_special_needs_person')->default(false);
          $table->string('marital_status')->nullable();
          $table->string('scholarity')->nullable();
          $table->string('father_name')->nullable();
          $table->string('mother_name')->nullable();
          $table->string('nationality')->nullable();

          $table->string('personal_email')->nullable()->unique();
          $table->string('business_email')->nullable()->unique();

          $table->string('phone')->nullable();
          $table->string('cellphone')->nullable();
          $table->string('emergency_phone')->nullable();

          $table->string('department')->nullable();
          $table->string('position')->nullable();
          $table->string('type_of_contract')->nullable();
          $table->string('salary')->nullable();
          $table->date('admission_date')->nullable();
          $table->string('direct_superior_name')->nullable();
          $table->string('hierarchical_degree')->nullable();

          $table->text('observations')->nullable();
          $table->date('contract_start_date')->nullable();
          $table->date('contract_expiration')->nullable();

          $table->string('cpf')->nullable()->unique();
          $table->string('rg')->nullable();
          $table->string('cnh')->nullable()->unique();
          $table->string('reservista')->nullable();
          $table->string('titulo_eleitor')->nullable();
          $table->string('zona_eleitoral')->nullable();
          $table->string('pis_ctps_numero')->nullable();
          $table->string('ctps_serie')->nullable();

          $table->string('banco')->nullable();
          $table->string('agencia')->nullable();
          $table->string('conta_corrente')->nullable();

          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
