<?php

namespace App\Http\Controllers\Api\Wllet;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactions = Transaction::whereRelation('wallet','user_id', $user->id)->paginate();
        $transactions->getCollection()->transform(function ($transaction) {
            return [
                'id' => $transaction->id,
                'amount' => (int)$transaction->amount,
                'type' => $transaction->type,
                'date' => $transaction->created_at->format('Y-m-d'),
                'time' => $transaction->created_at->format('H:i'),
            ];
        });
        return api_response($transactions);

    }
    public function show()
    {
        $user = auth()->user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        $balance = $wallet->balance;
        return api_response(['value'=>$balance]);

    }
}
