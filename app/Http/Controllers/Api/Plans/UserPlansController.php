<?php

namespace App\Http\Controllers\Api\Plans;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanHistory;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserPlansController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->input('type');
        $plan = UserPlan::where('user_id', $user->id)->whereRelation('plan' , 'plan_type' , $type)->get();
        $plans = $plan->map(function ($plan) {
            $expiresAt = Carbon::parse($plan->expires_at); // تاریخ انقضا
            $today = Carbon::now();
            $daysLeft = $today->diffInDays($expiresAt, false);
            return [
                'id' => $plan->id,
                'color' => $plan->plan->color,
                'original_price' => $plan->plan->original_price,
                'discount_amount' => $plan->plan->discount_amount,
                'expires_at' => $plan->expires_at->format('j F Y'),
                'days_left' => (int)$daysLeft . ' Days',
                'name' => $plan->plan->name,
                'price' => $plan->plan->price,
                'class_count' =>$plan->class_count.' left of ' . $plan->plan->class_count ,
            ];
        })->toArray();
        return api_response(['data' => $plans]);

    }
    public function history()
    {
        $user = auth()->user();
        $plan = PlanHistory::whereRelation('userPlan', 'user_id', $user->id)->paginate(10);
        $plan->getCollection()->transform(function ($plan) {
            return [

                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'plan_name' => $plan->userPlan->plan->name,
                'date' => $plan->created_at->format('Y-m-d'),
                'time' => $plan->created_at->format('H:i'),

            ];
        });
        return api_response($plan);

    }




}
