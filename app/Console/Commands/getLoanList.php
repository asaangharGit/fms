<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
class getLoanList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getLoanList:cron';

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

        $response = $client->get('https://lms.asaanghar.com/ops/api/loanList');
        $list = json_decode($response->getBody(), true)['data'];

        foreach($list as $val):

            $data['id'] = $val['id'];
            $data['name'] = $val['fname'].' '.$val['mname'].' '.$val['lname'];
            $data['account_no'] = $val['account_no'];
            $data['total_amount'] = $val['total_amount'];
            $data['profit'] = $val['total_amount_mu'];
            $data['principal'] = $val['total_amount_pr'];
            $data['disbursed_date'] = $val['disb_date'];
            $data['upcoming_installment_date'] = $val['loan_due']['due_date'];
            $data['upcoming_installment_payment'] = $val['loan_due']['payment_status'];

            if(\App\Models\LoanList::Where('id',$val['id'])->count() == 0){
                \App\Models\LoanList::create($data);
            }


        endforeach;

    }
}
