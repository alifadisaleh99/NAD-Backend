<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanEarningsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subscriptions = $this->subscriptions()->whereDate('created_at', '>=', Carbon::today()->subDays(7))->get();
        $price_subscriptions = 0.00;

        foreach ($subscriptions as $subscription) {
            $price_subscriptions += $subscription->plan_price;
        }

        return [
            'plan_id' => $this->id,
            'plan_earnings_last_7_days' => $price_subscriptions,
        ];
    }
}  
