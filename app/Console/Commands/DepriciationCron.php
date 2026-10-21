<?php

namespace App\Console\Commands;

use App\Helpers\Qs;
use App\Models\AssetDepreciation;
use App\Models\AssetRegistration;
use App\Models\AssetImpairment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DepriciationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'depriciation:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $year = Qs::getCurrentSession();


            $asset_reg = AssetRegistration::where('status',1)->where('active',1)->get();

            foreach ($asset_reg as $val):
                //Checking monthly(12) entries
                $deprecication = AssetDepreciation::where('item_id', $val->item_id)->where('year', $year);

                $total_value = $deprecication->value('jan')+$deprecication->value('feb')+
                    $deprecication->value('mar')+$deprecication->value('apr')+$deprecication->value('may')
                    +$deprecication->value('june')+$deprecication->value('july')+$deprecication->value('aug')
                    +$deprecication->value('sept')+$deprecication->value('oct')+
                    $deprecication->value('nov')+$deprecication->value('dec');

                $imp =  AssetImpairment::where('asset_register_id',$val->id)->sum('amount');
                $total = $val->amount-$imp;
                $data =  [   'item_id' => $val->item_id,
                    'asset_date' => $val->created_on,
                    'year' => $year,
                    'created_on' => date('Y-m-d'),
                    'dep_amount' => $val->amount,
                    'impairment' => $imp,
                    'total'=>$total,
                    'dep_percentage'=> $val->percent,
                    'percent_of_amount'=>($val->percent / 100) * $total,
                    'nrv'=>$total-$total_value
                ];

                $current_month = date('m');
                if($current_month == "01"){ $data['jan'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "02"){ $data['feb'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "03"){ $data['mar'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "04"){ $data['apr'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "05"){ $data['may'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "06"){ $data['june'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "07"){ $data['july'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "08"){ $data['aug'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "09"){ $data['sept'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "10"){ $data['oct'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "11"){ $data['nov'] = (($val->percent / 100) *$total)/12;}
                elseif($current_month == "12"){ $data['dec'] = (($val->percent / 100) *$total)/12;}

                if($deprecication->count() > 0 ):
                    echo "a";
                    AssetDepreciation::where('item_id',$val->item_id)->where('year', $year)->update($data);

                else:
                    echo "b";
                    AssetDepreciation::create($data);

                endif;

            endforeach;

    }
}
