<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/employees/{last_emp_no?}/{limit?}', function ($lastEmpNo = null, $limit = 50) {
    // Jika lastEmpNo kosong, ambil nilai dari baris pertama di database
    if (is_null($lastEmpNo)) {
        $firstRow = DB::table('employees')->orderBy('emp_no', 'asc')->first();
        $lastEmpNo = $firstRow ? $firstRow->emp_no - 1 : 0; // Jika ada data, mulai dari sebelum emp_no pertama, jika tidak mulai dari 0
    }
    $data = DB::table('employees')
        ->where('emp_no', '>', $lastEmpNo)
        ->orderBy('emp_no', 'asc')
        ->limit($limit)
        ->get();

    // Ambil ID terakhir dari data yang diambil
    $newLastEmpNo = $data->isEmpty() ? $lastEmpNo : $data->last()->emp_no;

    // Format respons
    return response()->json([
        'success' => true,
        'data' => $data,
        'last_id' => $newLastEmpNo, // ID terakhir untuk sinkronisasi berikutnya
        'has_more' => !$data->isEmpty(), // Indikator apakah masih ada data
    ]);
});

Route::get('/salaries', function () {
    $data = \DB::table('salaries')->paginate(10);
    return $data;
});
