<?php

namespace App\Http\Controllers;

use App\Bidbond;
use App\Company;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function bidbond_summary(Request $request)
    {
        $start = $request->start ?? Carbon::today()->addMonths(-1);
        $end = $request->end ?? Carbon::now();

        $companies = Company::approved()
            ->whereBetween('updated_at', [$start, $end])
            ->selectRaw('count(id) as companies')
            ->selectRaw('DATE_FORMAT(updated_at,"%Y-%m-%d") as date')
            ->groupBy('date')
            ->get();

        $company_dates = $companies->pluck('date')->all();

        $bidbonds = Bidbond::paid()
            ->whereBetween('updated_at', [$start, $end])
            ->selectRaw('count(id) as bidbonds')
            ->selectRaw('DATE_FORMAT(updated_at,"%Y-%m-%d") as date')
            ->groupBy('date')
            ->get();

        $tenders = Bidbond::paid()
            ->whereBetween('expiry_date', [$start, $end])
            ->selectRaw('count(tender_no) as tenders')
            ->selectRaw('DATE_FORMAT(expiry_date,"%Y-%m-%d") as date')
            ->groupBy('date')
            ->get();

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            if (!in_array($date->format('Y-m-d'), $company_dates)) {
                $companies->push(["date" => $date->format('Y-m-d'), "companies" => 0]);
            }
        }

        $results = $companies->map(function ($company) use ($bidbonds, $tenders) {

            $bidbond = $bidbonds->firstWhere('date', $company['date']);

            $company['bidbonds'] = $bidbond ? $bidbond['bidbonds'] : 0;

            $tender = $tenders->firstWhere('date', $company['date']);

            $company['tenders'] = $tender ? $tender['tenders'] : 0;

            return $company;

        })->sortBy('date')->values()->all();

        return response()->json($results);
    }
}
