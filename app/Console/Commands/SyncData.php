<?php

namespace App\Console\Commands;
use App\PostalCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use function in_array;

class SyncData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Data from the other services';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last_updated = cache('last_updated', now()->startOfYear());

        $excludes = ["migrations","failed_jobs", "bouncer"];

        $db_tables = [];

        $files = array_diff(scandir(database_path('migrations')), array('.', '..'));

        foreach ($files as $file){
            $first_index = strpos($file, 'create_') + 7;
            $last_index = strpos($file, '_table');
            $table_name = substr($file, $first_index, $last_index - $first_index);
            array_push($db_tables, $table_name);
        }

        $db_gateway_tables = DB::connection('gateway')->select('SHOW TABLES');
        $db_payment_tables = DB::connection('payment')->select('SHOW TABLES');
        $db_company_tables = DB::connection('company')->select('SHOW TABLES');
        $db_bidbond_tables = DB::connection('bidbond')->select('SHOW TABLES');

        $db_tables = array_merge($db_tables, ['abilities','roles','assigned_roles', 'permissions']);

        foreach ($db_tables as $table){

            if(!in_array($table, $excludes)){
                $connection = null;
                $to_array = json_decode(json_encode($db_gateway_tables),true);
                $flatten_arr = collect($to_array)->flatten()->toArray();
                $count = in_array($table,$flatten_arr);
                if($count){
                    $connection = "gateway";
                } else {
                    $to_array = json_decode(json_encode($db_payment_tables),true);
                    $flatten_arr = collect($to_array)->flatten()->toArray();
                    $count = in_array($table,$flatten_arr);
                    if($count){
                        $connection = "payment";
                    } else {
                        $to_array = json_decode(json_encode($db_company_tables),true);
                        $flatten_arr = collect($to_array)->flatten()->toArray();
                        $count = in_array($table,$flatten_arr);
                        if($count){
                            $connection = "company";
                        } else {
                            $to_array = json_decode(json_encode($db_bidbond_tables),true);
                            $flatten_arr = collect($to_array)->flatten()->toArray();
                            $count = in_array($table,$flatten_arr);
                            if($count){
                                $connection = "bidbond";
                            }
                        }
                    }
                }

                $latest = DB::table($table)->orderBy('id', 'desc')->first();

                if($latest){
                    $latest_id = $latest->id;
                } else {
                    $latest_id = 0;
                }

                $local_table = $table;

                if( $table == "bidbond_companies"){
                    $connection = "bidbond";
                    $table = "companies";
                    $local_table = "bidbond_companies";
                }

                if($latest_id === 0){
                    $new_items = DB::connection($connection)
                        ->table($table)
                        ->get()
                        ->toJson();


                    if($new_items){
                        DB::table($local_table)->insert(
                            json_decode($new_items, true)
                        );
                    }

                } else {
                        $updated_items_raw = DB::connection($connection)
                        ->table($table)
                        ->where(function ($query)  use($latest_id){
                            $query->where('id','<=', $latest_id);
                            $query->whereColumn('updated_at', '>', 'created_at');
                        })->orWhere(function ($query)  use($latest_id){
                            $query->where('id','<=', $latest_id);
                            $query->where('created_at', null);
                            $query->where('updated_at', null);
                        })->get()
                        ->toJson();

                    $updated_items = json_decode($updated_items_raw, true);
                    if($updated_items){
                        foreach ($updated_items as $updated_item){
                            DB::table($local_table)
                                ->where('id', $updated_item['id'])
                                ->update($updated_item);
                        }
                    }
                }
            }
        }

        cache()->forever('last_updated', now());

    }
}
