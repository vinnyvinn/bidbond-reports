<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;


class BidBondService
{

    use ConsumesExternalService;

    public $baseUri;

    public $secret;


    public function __construct()
    {
        $this->baseUri = config('services.bidbonds.base_uri');
        $this->secret = config('services.bidbonds.secret');
    }
    public static function init(){
        return new self();
    }

    public function obtainBidBonds()
    {
        return $this->performRequest('GET', '/all-bid-bonds');
    }
}
