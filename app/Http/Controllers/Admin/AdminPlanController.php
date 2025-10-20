<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type'); // دریافت نوع پلن از URL

        $query = Plan::query();

        if ($type) {
            $query->where('plan_type', $type);
        }

        $plans = $query->get();

        $planTypes = [
            'one_to_one' => 'خصوصی',
            'group' => 'گروهی',
            'webinar' => 'وبینار',
            'placement_test' => 'تست تعیین سطح',
            'mock_test' => 'آزمون شبیه‌سازی',
            'final_exam' => 'آزمون نهایی',
            'exam' => 'امتحان',
        ];

        return view('admin.plans.index', compact('plans', 'planTypes', 'type'));
    }
    public function create()
    {
        $planTypes = [
            'one_to_one' => 'خصوصی',
            'group' => 'گروهی',
            'webinar' => 'وبینار',
            'placement_test' => 'تست تعیین سطح',
            'mock_test' => 'آزمون شبیه‌سازی',
            'final_exam' => 'آزمون نهایی',
            'exam' => 'امتحان',
        ];

        return view('admin.plans.create', compact('planTypes'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'plan_type' => 'required|in:one_to_one,group,webinar,placement_test,mock_test,final_exam,exam',
            'color' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'class_count' => 'required|integer|min:0',
            'days' => 'required|integer|min:1',
        ]);

        \App\Models\Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'پلن با موفقیت ایجاد شد.');
    }
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'پلن با موفقیت حذف شد.');
    }
    public function users($id)
    {
        $plan = Plan::findOrFail($id);

        $users = \App\Models\UserPlan::with('user')
            ->where('plan_id', $id)
            ->latest()
            ->get();

        return view('admin.plans.users', compact('plan', 'users'));
    }

}
