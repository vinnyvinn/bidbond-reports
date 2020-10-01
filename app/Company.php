<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{

    protected $appends = ['date_created','postal'];
    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'id', 'company_id');
    }

    public function bidbonds()
    {
        return $this->hasMany(Bidbond::class);
    }

    public function scopeApproved($builder): void
    {
        $builder->where('approval_status', 'approved');
    }

    public function postal_code()
    {
        return $this->belongsTo('App\PostalCode');
    }
    public function rm()
    {
     return $this->belongsTo('App\User','relationship_manager_id','id');
    }

    public function getDateCreatedAttribute(){
     return Carbon::parse($this->created_at)->format('d/m/Y');
    }

    public function getPostalAttribute(){
      $postal = $this->postal_code()->first();
     return isset($postal) ? $postal->code : '';
    }
    public function scopeUniqueId($builder, $company_id): void
    {
        $builder->where('company_unique_id', $company_id);
    }
}
