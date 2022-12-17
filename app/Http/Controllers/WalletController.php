<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::all();
        return response()->json($wallets);
    }
}
