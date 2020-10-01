<?php

namespace App\Http\Controllers;

use App\Bidbond;
use App\Http\Resources\BidbondResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         $dashboard = BidbondResource::collection(Bidbond::GroupByMonth()->get());
         $comapany_summary = BidbondResource::collection(Bidbond::GroupByRm()->get());
        $bid_summary = [
            ['date' => '05/01/2019',
                'companies' => '2',
                'bidbonds_no' => '5',
                'tenders_closing' => '1'
            ],
            ['date' => '05/02/2019',
                'companies' => '1',
                'bidbonds_no' => '4',
                'tenders_closing' => '0'
            ],
            ['date' => '05/04/2019',
                'companies' => '4',
                'bidbonds_no' => '1',
                'tenders_closing' => '1'
            ],
            ['date' => '05/05/2019',
                'companies' => '0',
                'bidbonds_no' => '0',
                'tenders_closing' => '2'
            ],
            ['date' => '05/06/2019',
                'companies' => '3',
                'bidbonds_no' => '3',
                'tenders_closing' => '1'
            ],
        ];

        $work_days = [
            ['rm' => 'KP','date' => '01/01/2019','bidbonds_no'=>'97','bidbonds_no_avg'=>'0.24','bidbonds_value_avg'=>'98780'],
            ['rm' => 'BG','date' => '02/01/2019','bidbonds_no'=>'49','bidbonds_no_avg'=>'0.12','bidbonds_value_avg'=>'47197'],
            ['rm' => 'JK','date' => '03/01/2019','bidbonds_no'=>'0','bidbonds_no_avg'=>'0','bidbonds_value_avg'=>'0'],
            ['rm' => 'JK','date' => '04/01/2019','bidbonds_no'=>'34','bidbonds_no_avg'=>'0.08','bidbonds_value_avg'=>'40303'],
            ['rm' => 'JK','date' => '05/01/2019','bidbonds_no'=>'2','bidbonds_no_avg'=>'0.00','bidbonds_value_avg'=>'6112'],
        ];
            $bid_expiry = [
           ['date' =>'05/01/2019', 'expired_amount' =>'81982654','percent'=>100],
           ['date' =>'05/05/2019','expired_amount' =>0,'percent'=>0],
           ['date' =>'05/07/2019','expired_amount' =>0,'percent'=>0],
           ['date' =>'05/09/2019','expired_amount' =>0,'percent'=>0],
           ['date' =>'05/11/2019','expired_amount' =>0,'percent'=>0]
       ];
        return response()->json(['dashboard' =>$dashboard,'bid_summary'=>$bid_summary,'company_analysis'=>$comapany_summary,
            'days_worked'=>$work_days,'bid_expires'=>$bid_expiry]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
