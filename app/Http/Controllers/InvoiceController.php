<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoiceData = [
            [
                'id'=>'INV-001',
                'pasien'=>'Budi',
                'rm'=>'RM001',
                'status'=>'Masuk',
                'bayar'=>'Mandiri',
                'items'=>[
                    ['nama'=>'Paracetamol','qty'=>2,'harga'=>5000]
                ]
            ]
        ];

        return view('admin.invoice', compact('invoiceData'));
    }
}