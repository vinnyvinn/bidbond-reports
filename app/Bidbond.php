<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bidbond extends Model
{
    protected $fillable = ['created_at','updated_at'];
    protected $casts = [
        'paid' => 'bool'
    ];
    public function scopePaid($builder): void
    {
       $builder->where('paid',  true);
    }
    public function counterparty()
    {
        return $this->belongsTo(CounterParty::class, 'counter_party_id', 'id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','company_unique_id');
    }
    public function scopeGroupByMonth($builder){
        $builder->selectRaw('year(created_at) year, month(created_at) as month,date(created_at) as bid_date, count(*) bidbonds_no,
         sum(amount) as bid_exposure, sum(charge) collections, company_id,id')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc');

    }
    public function scopeGroupByRm($builder){
        return $builder->join('companies','companies.company_unique_id','=','bidbonds.company_id')
                       ->join('users','users.id','=','companies.relationship_manager_id')
                       ->select('bidbonds.created_at','users.firstname','users.lastname','companies.relationship_manager_id','bidbonds.id')
                       ->selectRaw('count(*) as bidbond_count,sum(amount) bidbond_value,count(distinct companies.id) as companies_count,
                        sum(bidbonds.charge-0.2*bidbonds.charge) commissions_value')
                      ->groupby('companies.relationship_manager_id');
    }
    public function scopeNonAgent($builder): void
    {
        $builder->whereNull('agent_id');
    }
    public function scopeActive($builder): void
    {
        $builder->whereNull("expired_at");
    }

}
