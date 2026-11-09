<?php

use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;

// Halaman awal diarahkan ke daftar pegawai
Route::redirect('/', '/pegawai');

// Satu baris ini membuat 7 route CRUD:
// index, create, store, show, edit, update, destroy
Route::resource('pegawai', PegawaiController::class);
