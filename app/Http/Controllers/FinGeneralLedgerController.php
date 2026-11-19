<?php

namespace App\Http\Controllers;

use App\Models\FinBanksAccount;
use App\Models\FinCheckbook;
use App\Models\FinGeneralLedger;
use App\Models\FinGeneralLedgerDetail;
use App\Models\FinVendors;
use App\User;
use App\Models\FinChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class FinGeneralLedgerController
 * @package App\Http\Controllers
 */
class FinGeneralLedgerController extends Controller {

    /** Voucher attachment types accepted on upload (documents, images, archives). */
    const ATTACHMENT_EXTENSIONS = [
        'pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
        'xls', 'xlsx', 'xlsm', 'csv', 'doc', 'docx', 'ppt', 'pptx', 'txt',
        'msg', 'eml', 'zip', 'rar', '7z',
    ];

    /** Upper limit of copies created by one "Duplicate" request. */
    const MAX_DUPLICATES = 50;

    public $ar = [1 => 'BPv', 'BRV', 'CPV', 'CRV', 'JV'];

    /**
     * Rejects attachments whose extension is not whitelisted (e.g. .php,
     * .html, .svg, .js, .exe) before anything is saved.
     */
    protected function validateAttachments(Request $request) {
        $request->validate([
            'filesupload.*' => ['file', 'max:20480', function ($attribute, $file, $fail) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (! in_array($extension, self::ATTACHMENT_EXTENSIONS, true)) {
                    $fail('Attachment type ".' . $extension . '" is not allowed.');
                }
            }],
        ]);
    }
    public $voucher_status = [1 => "Prepared", "Checked", "Approved", "Recieved"];

    public function filemanager() {
        //echo app_path() . '/helpers/filemanager/config/config.php';
        //die;
        $config = include_once(app_path() . '\helpers\filemanager\config\config.php');
        $languages = include_once(app_path() . '\helpers\filemanager\lang\languages.php');
        $utils = include_once(app_path() . '\helpers\filemanager\include\utils.php');
        return view('filemanager.dialog', compact('config', 'utils', 'languages'));
    }

    public function upload() {

    }

    public function getDocuments($id) {
        $finGeneralLedger = FinGeneralLedger::where('fin_general_ledgers.id', $id)->with('ledgerdetails')->first();
        return view('fin-general-ledger.showvoucher', compact('finGeneralLedger'));
    }

    public function getVoucherStatus($status) {
        $statusAr = [1 => "Prepared", "Checked", "Approved", "Recieved"];
        $LoanStatus = "";
        $title = $statusAr[$status];

        $class = [1 => "warning", 2 => "secondary", 3 => "primary", 4 => "success"];
        return '<span class="badge badge-pill badge-' . ($class[$status]) . '">' . $title . '</span>';
    }

    public static function addDigits($i, $zeroes) {
        // 1 - 5
        $ret = '';
        $num = mb_strlen($i);
        $Net = $zeroes - $num;
        for ($j = 0; $j < $Net; $j++) {
            $ret .= "0";
        }
        return $ret . $i;
    }

    public static function getReference($reference, $i, $zeroes) {
        $ar = [1 => 'BPV', 'BRV', 'CPV', 'CRV', 'JV'];
        $reference = $ar[$reference];
        return $reference . "" . self::addDigits($i, $zeroes);
    }

    public static function getVoucherHeading($reference) {
        $ar = [1 => 'BANK PAYMENT VOUCHER', 'BANK RECEIPT VOUCHER', 'CASH PAYMENT VOUCHER', 'CASH RECEIPT VOUCHER', 'JOURNAL VOUCHER'];
        $reference = $ar[$reference];
        $value = "9843";
        //echo self::convertNumberToWord($value);

        return $reference;
    }

    static function getChartOfAccountTitle($id) {
        $COA_Data = FinChartOfAccount::where('id', $id)->select('code', 'title')->first();
        if ($COA_Data) {
            return $COA_Data->code . " - " . $COA_Data->title;
        } else {
            return "";
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function process($id) {

        $finGeneralLedger = FinGeneralLedger::where('fin_general_ledgers.id', $id)->with('ledgerdetails')->first();
        $GJH_Result = \App\Models\FinGeneralLedgerHistory::where('finGeneralJournalId', $id)->with('user')->get();

        return view('fin-general-ledger.process', compact('finGeneralLedger', 'GJH_Result'));
    }

    public function index() {
        $finGeneralLedgers = FinGeneralLedger::whereIn('voucher_status', [3, 4])->get();
        return view('fin-general-ledger.index', compact('finGeneralLedgers'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $finGeneralLedger = new FinGeneralLedger();
        $chartOfAccounts = FinChartOfAccount::where("level", "L5")->orderBy("code")->get();
        $TxnDate = date("Y-m-d");
        $Reference = "BPV001";
        $vendors = FinVendors::where('status',1)->get();
        $banks = FinCheckbook::where('status',1)->get();
        return view('fin-general-ledger.create', compact('banks','vendors','finGeneralLedger', 'chartOfAccounts', 'Reference', 'TxnDate'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        request()->validate(FinGeneralLedger::$rules);
        $this->validateAttachments($request);
        extract($request->all(), EXTR_SKIP); // never overwrite existing variables such as $request

        $file = $request->file('filesupload');

        $LastSeries = FinGeneralLedger::where(['txn_type' => $reference])->orderBy("id", "desc")->first();
        if (!$LastSeries) {
            $LastSeries = 1;
        } else {
            $LastSeries = $LastSeries->txn_series + 1;
        }

        $TxnDate = date("Y-m-d", strtotime($TxnDate));
        $ChqNum = $chqnum;

        $userId = \Illuminate\Support\Facades\Auth::user()->id;

        //Maintain General Ledger
        $Model_GL = \App\Models\FinGeneralLedger::create([
                    'user_id' => $userId,
                    'details' => $purpose,
                    'debit' => 0,
                    'credit' => 0,
                    'txn_date' => $TxnDate,
                    'txn_type' => $reference,
                    'txn_series' => $LastSeries,
                    'cheque_number' => $ChqNum,
                    'office_id' => 1,
                    'vendor_id'=>$vendor_id,
                    'invoices_id'=>$invoices_id,
                    'cheque_id' => $cheque_id,
        ]);
         $FinGL_Id = $Model_GL->id;

        //Move Uploaded File
        $destinationPath = base_path() . '/uploads/' . $FinGL_Id;
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($file) {
            foreach ($file as $key => $file) {
                $fileName = date("YmdHis") . "_" . $FinGL_Id . "_" . $key . "." . $file->getClientOriginalExtension();
                //$file->move($destinationPath, $file->getClientOriginalName());
                $file->move($destinationPath, $fileName);
            }
        }
        $TotalDebit = 0;
        $TotalCredit = 0;
        foreach ($chartofaccount as $key => $val) {

                $TotalDebit += $request->debit[$key];
                $TotalCredit += $request->credit[$key];

                \App\Models\FinGeneralLedgerDetail::create([
                    'fin_gen_id' => $FinGL_Id,
                    'detail' => $request->detail[$key],
                    'coa_id' => $val,
                    'debit' => $request->debit[$key],
                    'credit' => $request->credit[$key]
                ]);

        }
        FinGeneralLedger::find($FinGL_Id)->update(['debit' => $TotalDebit, 'credit' => $TotalCredit]);

        return redirect()->route('fin-general-ledgers.vouchers')->with('flash_success', 'Record Created Successfully.');
    }

    public function clone(Request $request) {

        request()->validate(FinGeneralLedger::$rules);
        $this->validateAttachments($request);
        extract($request->all(), EXTR_SKIP); // never overwrite existing variables such as $request

        $file = $request->file('filesupload');

        $LastSeries = FinGeneralLedger::where(['txn_type' => $reference])->orderBy("id", "desc")->first();
        if (!$LastSeries) {
            $LastSeries = 1;
        } else {
            $LastSeries = $LastSeries->txn_series + 1;
        }

        $TxnDate = date("Y-m-d", strtotime($TxnDate));
        $ChqNum = $chqnum;

        $userId = \Illuminate\Support\Facades\Auth::user()->id;

        //Maintain General Ledger
        $Model_GL = \App\Models\FinGeneralLedger::create([
            'user_id' => $userId,
            'details' => $purpose,
            'debit' => 0,
            'credit' => 0,
            'txn_date' => $TxnDate,
            'txn_type' => $reference,
            'txn_series' => $LastSeries,
            'voucher_status' => 3,
           // 'cheque_number' => $ChqNum,
            'office_id' => 1,
           // 'vendor_id'=>$vendor_id,
           // 'invoices_id'=>$invoices_id,
           // 'cheque_id' => $cheque_id,
        ]);
        $FinGL_Id = $Model_GL->id;

        //Move Uploaded File
        $destinationPath = base_path() . '/uploads/' . $FinGL_Id;
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($file) {
            foreach ($file as $key => $file) {
                $fileName = date("YmdHis") . "_" . $FinGL_Id . "_" . $key . "." . $file->getClientOriginalExtension();
                //$file->move($destinationPath, $file->getClientOriginalName());
                $file->move($destinationPath, $fileName);
            }
        }
        $TotalDebit = 0;
        $TotalCredit = 0;
        foreach ($chartofaccount as $key => $val) {

            $TotalDebit += $request->debit[$key];
            $TotalCredit += $request->credit[$key];

            \App\Models\FinGeneralLedgerDetail::create([
                'fin_gen_id' => $FinGL_Id,
                'detail' => $request->detail[$key],
                'coa_id' => $val,
                'debit' => $request->debit[$key],
                'credit' => $request->credit[$key]
            ]);

        }
        FinGeneralLedger::find($FinGL_Id)->update(['debit' => $TotalDebit, 'credit' => $TotalCredit]);

        return redirect()->route('fin-general-ledgers.vouchers')->with('flash_success', 'Record Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $finGeneralLedger = FinGeneralLedger::where('fin_general_ledgers.id', $id)->with('ledgerdetails')->first();
        $vendors = FinVendors::where('id',$finGeneralLedger->vendor_id);


        return view('fin-general-ledger.show', compact('finGeneralLedger','vendors'));
    }

    public function show2($id) {
        $finGeneralLedger = FinGeneralLedger::where('fin_general_ledgers.id', $id)->with('ledgerdetails')->first();
        $vendors = FinVendors::where('id',$finGeneralLedger->vendor_id);
        $vendor_name = '';
        if($vendors->count() > 0){
            $vendor_name = $vendors->value('name');
        }

        return view('fin-general-ledger.show2', compact('finGeneralLedger','vendors','vendor_name'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function submitvouchers(Request $request) {
        $userId = \Illuminate\Support\Facades\Auth::user()->id;
        $userComment = $request->get("userComment");
        $gj_id = $request->get("gj_id");

          FinGeneralLedger::where('id', $gj_id)->update(['voucher_status' => 3]);

        $insert = \App\Models\FinGeneralLedgerHistory::create(['finGeneralJournalId' => $gj_id, 'ProcessComment' => $userComment, 'ProcessTo' => 1,'ProcessBy' => $userId]);

        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    public function edit($id) {
        $vendors = FinVendors::where('status',1)->get();
        $finGeneralLedger = FinGeneralLedger::find($id);
        $chartOfAccounts = FinChartOfAccount::where("level", "L5")->orderBy("code")->get();
        $Reference = "BPV001";
        $banks = FinCheckbook::where('status',1)->get();
        $nextSeries = (int) FinGeneralLedger::where('txn_type', $finGeneralLedger->txn_type)->max('txn_series') + 1;
        $maxDuplicates = self::MAX_DUPLICATES;

        return view('fin-general-ledger.edit', compact('finGeneralLedger','banks', 'chartOfAccounts', 'Reference','vendors', 'nextSeries', 'maxDuplicates'));

//
//        $finGeneralLedger = FinGeneralLedger::find($id);
//
//        return view('fin-general-ledger.edit', compact('finGeneralLedger'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  FinGeneralLedger $finGeneralLedger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinGeneralLedger $finGeneralLedger) {
        request()->validate(FinGeneralLedger::$rules);
        $this->validateAttachments($request);
        extract($request->all(), EXTR_SKIP); // never overwrite existing variables such as $request

        $file = $request->file('filesupload');

        $userId = \Illuminate\Support\Facades\Auth::user()->id;
        $Model_GL = \App\Models\FinGeneralLedger::where('id',$fin_id)
        ->update([
            'user_id' => $userId,
            'details' => $purpose,
            'debit' => 0,
            'credit' => 0,
            'txn_date' => $TxnDate,
            'txn_type' => $reference,

            'cheque_number' => $chqnum,
            'office_id' => 1,
            'vendor_id'=>$vendor_id,
            'invoices_id'=>$invoices_id,
            'cheque_id' => $cheque_id,
            ]);
        $FinGL_Id = $fin_id;

        //Move Uploaded File
        $destinationPath = base_path() . '/uploads/' . $FinGL_Id;
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($file) {
            foreach ($file as $key => $file) {
                $fileName = date("YmdHis") . "_" . $FinGL_Id . "_" . $key . "." . $file->getClientOriginalExtension();
                //$file->move($destinationPath, $file->getClientOriginalName());
                $file->move($destinationPath, $fileName);
            }
        }
        $TotalDebit = 0;
        $TotalCredit = 0;
        \App\Models\FinGeneralLedgerDetail::where("fin_gen_id",$FinGL_Id)->delete();
        foreach ($chartofaccount as $key => $val) {
            if (isset($val)) {

                $TotalDebit += $debit[$key];
                $TotalCredit += $credit[$key];

                $data1= ['fin_gen_id' => $FinGL_Id,'detail'=>$detail[$key], 'coa_id' => $val, 'debit' => $debit[$key], 'credit' => $credit[$key]];

                \App\Models\FinGeneralLedgerDetail::insert($data1);
            }
        }
        FinGeneralLedger::where('id',$FinGL_Id)->update(['debit' => $TotalDebit, 'credit' => $TotalCredit]);


        return redirect()->route('fin-general-ledgers.vouchers')
                        ->with('flash_success', 'FinGeneralLedger updated successfully');
    }

    /**
     * Creates copies of a saved voucher (header + all lines).
     *
     * Each copy gets the next number of its voucher type and starts as
     * "Prepared", so it goes through the normal check/approve workflow.
     * Cheque, invoice and attachments are not copied: they belong to the
     * original transaction and copying them would pay the same cheque or
     * invoice twice.
     */
    public function duplicate(Request $request, $id) {
        $original = FinGeneralLedger::with('ledgerdetails')->findOrFail($id);
        $source = self::getReference($original->txn_type, $original->txn_series, 3);

        $data = $request->validateWithBag('duplicate', [
            'copies' => 'required|integer|min:1|max:' . self::MAX_DUPLICATES,
            'date_mode' => 'required|in:original,custom',
            'txn_date' => 'required_if:date_mode,custom|nullable|date_format:Y-m-d',
        ], [
            'copies.max' => 'You can create at most ' . self::MAX_DUPLICATES . ' copies at a time.',
            'txn_date.required_if' => 'Please choose the transaction date for the copies.',
        ]);

        if ($original->ledgerdetails->isEmpty()) {
            return back()->with('flash_danger', $source . ' has no entries to copy.');
        }

        $txnDate = $data['date_mode'] === 'custom' ? $data['txn_date'] : $original->txn_date;
        $userId = \Illuminate\Support\Facades\Auth::id();
        $totalDebit = $original->ledgerdetails->sum('debit');
        $totalCredit = $original->ledgerdetails->sum('credit');

        $references = DB::transaction(function () use ($original, $data, $txnDate, $userId, $totalDebit, $totalCredit) {
            // Lock the series of this voucher type so parallel saves cannot reuse a number.
            $series = (int) FinGeneralLedger::where('txn_type', $original->txn_type)->lockForUpdate()->max('txn_series');
            $now = now();
            $references = [];

            for ($i = 0; $i < $data['copies']; $i++) {
                $series++;

                $copy = FinGeneralLedger::create([
                    'user_id' => $userId,
                    'details' => $original->details,
                    'debit' => $totalDebit,
                    'credit' => $totalCredit,
                    'txn_date' => $txnDate,
                    'txn_type' => $original->txn_type,
                    'txn_series' => $series,
                    'office_id' => $original->office_id,
                    'vendor_id' => $original->vendor_id,
                    'loan_id' => $original->loan_id,
                    'voucher_status' => 1,
                ]);

                FinGeneralLedgerDetail::insert($original->ledgerdetails->map(function ($line) use ($copy, $now) {
                    return [
                        'fin_gen_id' => $copy->id,
                        'coa_id' => $line->coa_id,
                        'detail' => $line->detail,
                        'debit' => $line->debit,
                        'credit' => $line->credit,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->all());

                $references[] = self::getReference($copy->txn_type, $copy->txn_series, 3);
            }

            return $references;
        });

        $created = count($references) === 1
            ? $references[0]
            : count($references) . ' vouchers (' . $references[0] . ' to ' . end($references) . ')';

        return redirect()->route('fin-general-ledgers.vouchers')
                        ->with('flash_success', $source . ' duplicated: ' . $created . ' created as Prepared.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id) {
         FinGeneralLedger::find($id)->delete();
         FinGeneralLedgerDetail::where('fin_gen_id',$id)->delete();


        return redirect()->route('fin-general-ledgers.index')
                        ->with('flash_success', 'FinGeneralLedger deleted successfully');
    }




    public function cloneVoucher(Request $req){

            $id = $req->segment(4);
            $finGeneralLedger = FinGeneralLedger::find($id);
            $chartOfAccounts = FinChartOfAccount::where("level", "L5")->orderBy("code")->get();
            $Reference = "BPV001";

            $data = FinGeneralLedger::where('cheque_number', '!=', 'NULL')->orderBy("id", "desc")->first();

            if ($data) {
                $chqnum = (int) $data->cheque_number;
                $chqnum += 1;
            } else {
                $chqnum = "1";
            }

//        $chqnum = "1";


            return view('fin-general-ledger.clone', compact('finGeneralLedger', 'chartOfAccounts', 'Reference','chqnum'));


        }



        public function bulkprintForm(){

            $d['datef'] = '';
            $d['datet'] = '';
            return view('fin-general-ledger.bulkprintForm',$d);


        }

        public function bulkprintAll(Request $request){

            if($request->type == 'all'){
                $finGeneralLedgers_all = FinGeneralLedger::whereBetween("txn_date", array($request->datefrom, $request->dateto), "and")->get();

            }else{
                $finGeneralLedgers_all = FinGeneralLedger::where('txn_type',$request->type)->whereBetween("txn_date", array($request->datefrom, $request->dateto), "and")->get();
            }
            return view('fin-general-ledger.bulkprintAll',compact('finGeneralLedgers_all'));


        }



        public function chequeNumExist(Request $request){
            $result = 0;

           $exist = FinGeneralLedger::where('cheque_id',$request->cheque_id)->where('cheque_number',$request->cheque_number)->count();
            if($exist > 0){

                return 2;
            }
            $cheques = FinCheckbook::where('id',$request->cheque_id)->get();

            foreach ($cheques as $val):
                if($request->cheque_number >= $val->checknum_start && $request->cheque_number <= $val->checknum_end ){

                    $result = 1;

                }


            endforeach;
            return $result;


        }
}
