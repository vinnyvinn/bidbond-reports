<?php

namespace App\Http\Controllers;

use App\Bidbond;
use App\RowCommision;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function break_down(Request $request)
    {
        $start = $request->start ?? Carbon::today()->addMonths(-1);
        $end = $request->end ?? Carbon::now();

        $commissions = RowCommision::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at,"%Y-%m-%d") as date')
            ->selectRaw('sum(commission_amount) as commission')
            ->groupBy('date')
            ->get();

        $bidbonds = Bidbond::query()
            ->whereBetween('deal_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(deal_date,"%Y-%m-%d") as date')
            ->selectRaw('count(id) as bidbonds')
            ->paid()
            ->groupBy('date')
            ->get();

        $bid_exposures = Bidbond::query()
            ->whereBetween('deal_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(deal_date,"%Y-%m-%d") as date')
            ->selectRaw('sum(amount) as exposure')
            ->paid()
            ->active()
            ->groupBy('date')
            ->get();

        $companies = Bidbond::query()
            ->whereBetween('deal_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(deal_date,"%Y-%m-%d") as date')
            ->selectRaw('count(distinct(company_id)) as companies')
            ->paid()
            ->groupBy('date')
            ->get();

        $company_count = Bidbond::query()
            ->whereBetween('deal_date', [$start, $end])
            ->selectRaw('count(distinct(company_id)) as companies')
            ->paid()
            ->first()->companies;

        $commission_dates = $commissions->pluck('date')->all();

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            if (!in_array($date->format('Y-m-d'), $commission_dates)) {
                $commissions->push(["date" => $date->format('Y-m-d'), "commission" => 0]);
            }
        }

        $results = $commissions->map(function ($commission) use ($bidbonds, $bid_exposures, $companies) {
            $bidbond = $bidbonds->firstWhere('date', $commission['date']);
            $commission['bidbonds'] = $bidbond ? $bidbond['bidbonds'] : 0;

            $bid_exposure = $bid_exposures->firstWhere('date', $commission['date']);
            $commission['exposure'] = $bid_exposure ? $bid_exposure['exposure'] : 0;

            $company = $companies->firstWhere('date', $commission['date']);
            $commission['companies'] = $company ? $company['companies'] : 0;

            return $commission;
        })->sortBy('date')->values()->all();

        return response()->json(["companies"=> $company_count, "results" => $results]);
    }


}
