<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->date('tgl_lahir');
            $table->unsignedInteger('gaji');
            $table->string('status')->default('tetap');
            $table->foreignId('jabatan_id')
                ->constrained('jabatans');
            $table->date('mulai_kontrak')->nullable();
            $table->date('akhir_kontrak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
