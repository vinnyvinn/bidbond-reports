<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bidbond;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$customers = User::whereIs('customer')->pluck('id');
$companies = \App\Company::pluck('company_id');

$factory->define(Bidbond::class, function (Faker $faker) use ($customers,$companies) {
    $period = array(30, 60, 90, 120, 150, 180)[random_int(0, 5)];
    $date = Carbon::now()->addDays(random_int(-180, 30));

    return [
        'tender_no' => $faker->word,
        'purpose' => $faker->domainWord,
        'addressee' => $faker->address,
        'effective_date' => $date,
        'reference' => $faker->word,
        'expiry_date' => $date->addDays($period),
        'amount' => random_int(100000, 9000000),
        'currency' => 'KES',
        'period' => $period,
        'company_id' => $faker['company'],
        'counter_party_id' => random_int(1, 200),
        'charge' => random_int(1500, 10000),
        'template_secret' => '5f1ad657b15bb',
        'created_by' => $customers->random(),
        'paid' => 1,
        'agent_id' => null
    ];
});
