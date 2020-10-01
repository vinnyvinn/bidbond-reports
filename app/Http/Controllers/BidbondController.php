<?php

namespace App\Http\Controllers;

use App\Bidbond;
use App\Company;
use App\RowCommision;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BidbondController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start ?? Carbon::today()->addMonths(-1);
        $end = $request->end ?? Carbon::now();

        $bidbonds = Bidbond::query()
            ->select('reference', 'expiry_date', 'deal_date', 'currency', 'amount', 'companies.name as company', 'counter_parties.name as counterparty')
            ->join('companies', 'companies.company_unique_id', 'bidbonds.company_id')
            ->join('counter_parties', 'counter_parties.id', 'bidbonds.counter_party_id')
            ->where('bidbonds.paid', 1)
            ->whereBetween('deal_date', [$start, $end])
            ->get();

        return response()->json($bidbonds);
    }


    public function expired(Request $request)
    {
        $start = $request->start ?? Carbon::today()->addMonths(-1);
        $end = $request->end ?? Carbon::now();

        $bidbonds = Bidbond::paid()
            ->whereBetween('expiry_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(expiry_date,"%Y-%m-%d") as date')
            ->selectRaw('sum(amount) as amount')
            ->groupBy('date')
            ->get();

        return response()->json($bidbonds);
    }

    public function byRM(Request $request)
    {
        $start = $request->start ?? Carbon::today()->addMonths(-1);
        $end = $request->end ?? Carbon::now();

        $bidbonds = Bidbond::query()
            ->select('id', 'amount', 'company_id')
            ->paid()
            ->nonAgent()
            ->whereBetween('deal_date', [$start, $end])
            ->get();

        $company_ids = $bidbonds->pluck('company_id')->unique()->values()->all();

        $companies = Company::query()
            ->select('relationship_manager_id', 'company_unique_id')
            ->whereIn('company_unique_id', $company_ids)
            ->get();

        $rm_ids = $companies->pluck('relationship_manager_id')->unique()->values()->all();

        $commissions = RowCommision::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('sum(commission_amount) as commission')
            ->selectRaw('user_id as rm_id')
            ->whereIn('user_id',$rm_ids)
            ->groupBy('user_id')
            ->get();

        $rms = User::query()
            ->select('id', 'firstname', 'lastname')
            ->whereIn('id', $rm_ids)
            ->get();

        $results = collect([]);

        $rms->each(function ($rm) use ($companies, $bidbonds, $commissions, &$results) {
            $rm_companies = $companies->where('relationship_manager_id', $rm->id)->pluck('company_unique_id')->unique()->values()->all();
            $rm_bidbonds = $bidbonds->whereIn('company_id', $rm_companies);
            $companies_count = count($rm_companies);
            $bidbonds_count = $rm_bidbonds->count('id');
            $bidbonds_value = $rm_bidbonds->sum('amount');
            $commission = $commissions->firstWhere('rm_id', $rm->id);
            $commission = $commission ? $commission->commission : 0;
            $results->push([
                "rm_id" => $rm->id,
                "name" => $rm->firstname . ' ' . $rm->lastname,
                "companies_count" => $companies_count,
                "bidbonds_count" => $bidbonds_count,
                "bidbonds_value" => $bidbonds_value,
                "commission" => $commission
            ]);
        });

        return response()->json($results);
    }


}
