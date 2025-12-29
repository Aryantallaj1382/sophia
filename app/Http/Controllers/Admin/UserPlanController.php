<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;

class UserPlanController extends Controller
{
    public function index()
    {
        $userPlans = UserPlan::with(['user', 'plan'])->latest()->paginate(20);

        return view('admin.user_plans.index', compact('userPlans'));
    }

    public function create()
    {
        $users = User::whereHas('student')->get();
        $plans = Plan::all();

        return view('admin.user_plans.create', compact('users', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'started_at' => 'required|date',
            'expires_at' => 'required|date|after_or_equal:started_at',
            'class_count' => 'required|integer|min:0',
        ], [
            'user_id.required' => 'انتخاب کاربر الزامی است.',
            'plan_id.required' => 'انتخاب پلن الزامی است.',
            'expires_at.after_or_equal' => 'تاریخ پایان باید بعد یا مساوی تاریخ شروع باشد.',
        ]);

        // جلوگیری از تکرار پلن فعال برای کاربر (اختیاری)
        $exists = UserPlan::where('user_id', $request->user_id)
            ->where('expires_at', '>', now())
            ->where('plan_id', $request->plan_id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'این کاربر در حال حاضر پلن فعال از این نوع دارد.'])->withInput();
        }

        UserPlan::create([
            'user_id' => $request->user_id,
            'plan_id' => $request->plan_id,
            'started_at' => $request->started_at,
            'expires_at' => $request->expires_at,
            'class_count' => $request->class_count,
        ]);

        return redirect()->route('admin.user-plans.index')
            ->with('success', 'پلن با موفقیت برای کاربر ثبت شد.');
    }
    public function edit($id)
    {
        $userPlan = UserPlan::findOrFail($id);
        $users = User::all();
        $plans = Plan::all();

        return view('admin.user_plans.edit', compact('userPlan', 'users', 'plans'));
    }

    public function update(Request $request, $id)
    {
        $userPlan = UserPlan::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'started_at' => 'required|date',
            'expires_at' => 'required|date',
            'class_count' => 'required|integer'
        ]);

        // اگر پلن جدید انتخاب شد → class_count و expires_at را رفرش کن در صورت نیاز
        if ($userPlan->plan_id != $data['plan_id']) {
            $plan = Plan::findOrFail($data['plan_id']);

            $data['expires_at'] = $request->input('expires_at', now()->addDays($plan->days));
            $data['class_count'] = $request->input('class_count', $plan->class_count);
        }

        $userPlan->update($data);

        return redirect()->route('admin.user-plans.index')->with('success', 'پلن کاربر ویرایش شد.');
    }

    public function show($id)
    {
        $userPlan = UserPlan::with(['user', 'plan'])->findOrFail($id);

        return view('admin.user_plans.show', compact('userPlan'));
    }

    public function destroy($id)
    {
        UserPlan::findOrFail($id)->delete();

        return redirect()->route('admin.user-plans.index')->with('success', 'پلن حذف شد.');
    }
}

