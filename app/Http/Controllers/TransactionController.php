<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TypeTransaction;
use App\Models\Wallet;
use App\Models\User;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all()->map(function ($transaction) {
            $transaction->typeTransaction = TypeTransaction::where('id', $transaction->type_transaction_id)->first();
            $transaction->wallet = Wallet::where('id', $transaction->wallet_id)->first();
            $transaction->user = $transaction->user_id ? User::where('id', $transaction->user_id)->first() : null;
            return $transaction;
        });
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->id;
        $wallet = Wallet::where('user_id', $user)->first();
        // type_transaction_id = 1 => Deposito
        // type_transaction_id = 2 => Transferencia
        // type_transaction_id = 3 => Retiro
        try {
            $rules = [
                'type_transaction_id' => 'required',
                'amount' => 'required|numeric|min:0',
            ];
            $messages = [
                'type_transaction_id.required' => 'The type transaction is required',
                'amount.required' => 'The amount is required',
                'amount.numeric' => 'The amount must be a number',
                'amount.min' => 'The amount must be greater than 0',
            ];

            if($request->type_transaction_id == 1) {
                $rules['amount'] = 'required|numeric|min:1';
                $messages['amount.min'] = 'The amount must be greater than 1';
            } else if ($request->type_transaction_id == 2) {
                $rules['amount'] = 'required|numeric|min:1';
                $rules['user_id'] = 'required';
                $messages['amount.min'] = 'The amount must be greater than 1';
                $messages['user_id.required'] = 'The user is required';
            } else if ($request->type_transaction_id == 3) {
                $rules['amount'] = 'required|numeric|min:1';
                $messages['amount.min'] = 'The amount must be greater than 1';
            }

            if($request->type_transaction_id == 2 || $request->type_transaction_id == 3) {
                if($wallet->balance < $request->amount) {
                    return response()->json(['message' => 'The amount is greater than the balance'], 400);
                }
            }

            if($request->type_transaction_id == 1) {
                $wallet->balance += $request->amount;
                $wallet->save();
            } else if ($request->type_transaction_id == 2) {
                $wallet->balance -= $request->amount;
                $wallet->save();
                $wallet2 = Wallet::where('user_id', $request->user_id)->first();
                $wallet2->balance += $request->amount;
                $wallet2->save();
            } else if ($request->type_transaction_id == 3) {
                $wallet->balance -= $request->amount;
                $wallet->save();
            }
            
            $this->validate($request, $rules, $messages);
            
            $transaction = Transaction::create([
                'type_transaction_id' => $request->type_transaction_id,
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'user_id' => $request->user_id,
            ]);
            return response()->json($transaction);
        } catch (\Throwable $th) {
            return response()->json($th);
        }
    }
}
