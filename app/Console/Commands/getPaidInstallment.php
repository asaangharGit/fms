<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
class getPaidInstallment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getPaidInstallment:cron';

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
        $client = new Client();

        $response = $client->get('https://lms.asaanghar.com/ops/api/allinstallmentList');
        $list = json_decode($response->getBody(), true)['data'];

        foreach($list as $val):

            $data['installment_id'] = $val['id'];
            $data['loan_id'] = $val['loan_id'];
            $data['recovered_date'] = $val['due_date'];
            $data['total_amount'] = $val['amount_total'];
            $data['profit'] = $val['amount_pr'];
            $data['principal'] = $val['amount_mu'];
            $data['penality'] = $val['amount_penalty'];
            $data['charity'] = 0;
            if(\App\Models\RecievableAmount::Where('installment_id',$val['id'])->count() == 0){
                \App\Models\RecievableAmount::create($data);
            }


        endforeach;


    }
}
