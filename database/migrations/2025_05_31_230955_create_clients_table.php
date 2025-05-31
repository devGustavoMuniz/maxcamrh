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
    Schema::create('clients', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
      $table->string('cnpj')->unique();
      $table->string('test_number');
      $table->date('contract_end_date');
      $table->boolean('is_monthly_contract')->default(false);
      $table->string('phone');
      $table->string('logo_url');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('clients');
  }
};
