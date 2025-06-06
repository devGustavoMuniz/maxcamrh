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
    Schema::create('addresses', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
      $table->string('cep');
      $table->string('street');
      $table->string('number');
      $table->string('complement')->nullable();
      $table->string('neighborhood');
      $table->string('state');
      $table->string('city');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('addresses');
  }
};
