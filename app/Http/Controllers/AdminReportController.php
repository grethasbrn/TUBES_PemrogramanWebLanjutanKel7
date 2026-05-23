<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pasien;

class AdminReportController extends Controller
{
    public function index()
    {
        // ambil semua invoice
        $transaksi = Invoice::latest()->get();

        // total pasien
        $totalPasien = Pasien::count();

        // total pendapatan
        $totalPendapatan = Invoice::sum('total_tagihan');

        return view('admin.report', compact(
            'transaksi',
            'totalPasien',
            'totalPendapatan'
        ));
    }
}