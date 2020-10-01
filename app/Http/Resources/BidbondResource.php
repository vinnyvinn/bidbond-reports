<?php

namespace App\Http\Resources;

use App\Bidbond;
use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BidbondResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'date_created' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'value_date' => Carbon::parse($this->effective_date)->format('d/m/Y'),
            'maturity_date' => Carbon::parse($this->expiry_date)->format('d/m/Y'),
            'category' => '28010',
            'category_name' => 'Performance Bond Issued',
            'currency' => 'KES',
            'amount' => $this->amount,
            'name' => self::companyName(self::find($this->id)),
            'bidbonds_no' => $this->bidbonds_no,
            'bid_exposure' => $this->bid_exposure,
            'collections' => $this->collections,
            'bidbond_count' => $this->bidbond_count,
            'bidbond_value' => $this->bidbond_value,
            'companies_count' => $this->companies_count,
            'commissions_value' => $this->commissions_value,
            'crp' => self::companyCpr(self::find($this->id)),
            'counter_party' => self::counterParty(self::find($this->id)),
            'customerid' => self::customerId(self::find($this->id)),
            'account' => self::accountId(self::find($this->id)),
            'excise_duty' => self::excerciseDuty($this->collections),
            'commissions' => self::commission($this->collections),
            'jbb' => self::jbb($this->collections),
            'mpf' => self::mpf($this->collections),
            'full_name' => $this->firstname . ' ' . $this->lastname,
            'date' => Carbon::parse($this->bid_date)->format('d/m/Y'),
        ];
    }


    public static function companyName(Bidbond $bidbond)
    {
        $company_name = $bidbond->company()->first();
        return isset($company_name->name) ? $company_name->name : '';
    }

    public static function companyCpr(Bidbond $bidbond)
    {
        $company_crp = $bidbond->company()->first();
        return isset($company_crp->crp) ? $company_crp->crp : '';
    }

    public static function counterParty(Bidbond $bidbond)
    {
        $counter_party = $bidbond->counterparty()->first();
        return isset($counter_party) ? $counter_party->name : '';
    }

    public static function customerId(Bidbond $bidbond)
    {
        $customerid = $bidbond->company()->first();
        return isset($customerid->customerid) ? $customerid->customerid : '';
    }

    public static function accountId(Bidbond $bidbond)
    {
        $account = $bidbond->company()->first();
        return isset($account->account) ? $account->account : '';
    }

    public static function excerciseDuty($collections)
    {
        return round(0.2 * (float)$collections, 2);
    }

    public static function commission($collections)
    {
        return round($collections - self::excerciseDuty($collections), 2);
    }

    public static function jbb($collections)
    {
        return round(0.7 * self::commission($collections), 2);
    }

    public static function mpf($collections)
    {
        return round(0.3 * self::commission($collections), 2);
    }

}
