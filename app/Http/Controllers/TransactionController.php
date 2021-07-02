<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function checkTransaction($id) { }

    public function index()
    {
        return view('payment.transactions.index');
    }
}
