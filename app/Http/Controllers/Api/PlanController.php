<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\PlanHistory;
use App\Models\UserPlan;
use Illuminate\Http\Request;
class PlanController extends Controller
{
    public function index(Request $request)
    {
        $planType = $request->query('type');

        $plans = Plan::query()
            ->when($planType, function ($query, $planType) {
                $query->where('plan_type', $planType);
            })
            ->orderBy('price') // مثلا مرتب بر اساس قیمت
            ->get();

        $a = $plans->map(function ($plan) {
            $daysLeft = $plan->days;
            return [
                'id' => $plan->id,
                'color' => $plan->color,
                'original_price' => (int)$plan->original_price,
                'discount_amount' => $plan->discount_amount,
                'days' =>$daysLeft .' Days',
                'name' => $plan->name,
                'price' => $plan->price,
                'class_count' => $plan->class_count,

            ];
        });


        return api_response([
            'data' => $a,
            ]
        );
    }
    public function show($id)
    {

        $plan = Plan::find($id);
        return api_response($plan->toArray());

    }

    public function buy($id)
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        $plan = Plan::find($id);
        $price = $plan->price;
        if ($wallet->balance < $price) {
            return api_response([],'you dont have enough balance');
        }
       $plan_user = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $id,
            'started_at' => now(),
             'expires_at' => now()->addDays((int)$plan->days),
            'class_count' => $plan->class_count,
        ]);
        PlanHistory::create([
            'user_plan_id' => $plan_user->id,
            'usable_type' => Plan::class,
            'usable_id' => $id,
            'price' => $price,
            'name' => $plan->name,
        ]);

        $wallet->balance -= $price;
        $wallet->save();
        return api_response([], 'success');


    }
}
