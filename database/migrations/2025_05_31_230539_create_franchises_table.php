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
    Schema::create('franchises', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
      $table->string('maxcam_email')->unique();
      $table->string('cnpj')->unique();
      $table->integer('max_client');
      $table->date('contract_start_date');
      $table->string('actuation_region');
      $table->string('document_url')->nullable();
      $table->text('observations');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('franchises');
  }
};
