<?php

namespace App\Http\Controllers;

use App\Bidbond;
use App\Company;
use App\Services\BidBondService;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return Company::approved()->get();
    }

    public function getBidbonds()
    {
     return Bidbond::all();
    }
}
