<?php

namespace App\Http\Controllers;

use App\Bidbond;
use App\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function companySearch(Request $request)
    {
        $start = $request->start ?? Carbon::today()->addMonths(-1);
        $end = $request->end ?? Carbon::now();

        $bidbonds = Payment::paid()
            ->whereBetween('expiry_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(expiry_date,"%Y-%m-%d") as date')
            ->selectRaw('sum(amount) as amount')
            ->groupBy('date')
            ->get();

        return response()->json($bidbonds);
    }
}
