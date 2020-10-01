<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;


class CompanyService
{

    use ConsumesExternalService;

    public $baseUri;

    public $secret;


    public function __construct()
    {
        $this->baseUri = config('services.companies.base_uri');

        $this->secret = config('services.companies.secret');
    }
    public static function initCompany(){
     return new self();
   }
    public function obtainAllCompanies()
    {
        return $this->performRequest('GET', '/all-companies');
    }

}
