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
        Schema::create('barangkeluar', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_keluar');
            $table->smallInteger('qty_keluar')->default(1);
            $table->foreignId('barang_id');
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangkeluar');
    }
};
