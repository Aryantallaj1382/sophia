<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // لیست کاربران با صفحه‌بندی
        $users = User::whereHas('student')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:completed,pending,failed',
        ]);

        $transaction->status = $request->status;
        $transaction->save();

        return back()->with('success', 'وضعیت تراکنش با موفقیت تغییر کرد.');
    }
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'operation_type' => 'required|in:deposit,withdraw',
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $user->wallet()->create(['balance' => 0]);
        }

        if ($data['operation_type'] === 'deposit') {
            $wallet->balance += $data['amount'];
        } else {
            if ($wallet->balance < $data['amount']) {
                return back()->with('error', 'موجودی کافی نیست.');
            }
            $wallet->balance -= $data['amount'];
        }

        $wallet->save();

        Transaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $data['amount'],
            'type' => $data['operation_type'],
            'status' => 'completed',
        ]);


        return back()->with('success', 'تراکنش با موفقیت ثبت شد.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'کاربر با موفقیت حذف شد.');
    }

}
