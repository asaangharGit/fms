<?php

namespace App\Helpers;
use App\Models\ActivityLogs;
use App\Models\FinGeneralLedger;
use Illuminate\Http\Request;
use App\Models\MainMenu;
use App\Models\Setting;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\SubMenu;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use DateTime;
use DatePeriod;
use DateInterval;
use GuzzleHttp\Client;
class Qs
{


    public static function displayError($errors)
    {
        foreach ($errors as $err) {
            $data[] = $err;
        }
        return '
                <div class="alert alert-danger alert-styled-left alert-dismissible">
									<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
									<span class="font-weight-semibold">Oops!</span> '.
        implode(' ', $data).'
							    </div>
                ';
    }

    public static function getAppCode()
    {
        return self::getSetting('system_title') ?: 'CJ';
    }

    public static function getDefaultUserImage()
    {
        return asset('public/global_assets/images/user.png');
    }

    public static function getPanelOptions()
    {
        return '    <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>';
    }

    public static function displaySuccess($msg)
    {
        return '
 <div class="alert alert-success alert-bordered">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button> '.
        $msg.'  </div>
                ';
    }

    public static function getTeamSA()
    {
        return ['admin', 'super_admin'];
    }

    public static function getTeamAccount()
    {
        return ['admin', 'super_admin', 'accountant'];
    }

    public static function getTeamSAT()
    {
        return ['admin', 'super_admin', 'teacher'];
    }

    public static function getTeamAcademic()
    {
        return ['admin', 'super_admin', 'teacher', 'student'];
    }

    public static function getTeamAdministrative()
    {
        return ['admin', 'super_admin', 'accountant'];
    }

    public static function hash($id)
    {
        $date = date('dMY').'CJ';
        $hash = new Hashids($date, 14);
        return $hash->encode($id);
    }

    public static function getUserRecord($remove = [])
    {
        $data = ['name', 'email', 'phone', 'phone2', 'dob', 'gender', 'address', 'bg_id', 'nal_id', 'state_id', 'lga_id'];

        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getStaffRecord($remove = [])
    {
        $data = ['emp_date',];

        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getStudentData($remove = [])
    {
        $data = ['my_class_id', 'section_id', 'my_parent_id', 'dorm_id', 'dorm_room_no', 'year_admitted', 'house', 'age'];

        return $remove ? array_values(array_diff($data, $remove)) : $data;

    }

    public static function decodeHash($str, $toString = true)
    {
        $date = date('dMY').'CJ';
        $hash = new Hashids($date, 14);
        $decoded = $hash->decode($str);
        return $toString ? implode(',', $decoded) : $decoded;
    }

    public static function userIsTeamAccount()
    {
        return in_array(Auth::user()->user_type, self::getTeamAccount());
    }

    public static function userIsTeamSA()
    {
        return in_array(Auth::user()->user_type, self::getTeamSA());
    }

    public static function userIsTeamSAT()
    {
        return in_array(Auth::user()->user_type, self::getTeamSAT());
    }

    public static function userIsAcademic()
    {
        return in_array(Auth::user()->user_type, self::getTeamAcademic());
    }

    public static function userIsAdministrative()
    {
        return in_array(Auth::user()->user_type, self::getTeamAdministrative());
    }

    public static function userIsAdmin()
    {
        return Auth::user()->user_type == 'admin';
    }

    public static function getUserType()
    {
        return Auth::user()->user_type;
    }

    public static function userIsSuperAdmin()
    {
        return Auth::user()->user_type == 'super_admin';
    }

    public static function userIsStudent()
    {
        return Auth::user()->user_type == 'student';
    }

    public static function userIsTeacher()
    {
        return Auth::user()->user_type == 'teacher';
    }

    public static function userIsParent()
    {
        return Auth::user()->user_type == 'parent';
    }

    public static function userIsStaff()
    {
        return in_array(Auth::user()->user_type, self::getStaff());
    }

    public static function getStaff($remove=[])
    {
        $data =  ['super_admin', 'admin', 'teacher', 'accountant', 'librarian'];
        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    public static function getAllUserTypes($remove=[])
    {
        $data =  ['super_admin', 'admin', 'teacher', 'accountant', 'librarian', 'student', 'parent'];
        return $remove ? array_values(array_diff($data, $remove)) : $data;
    }

    // Check if User is Head of Super Admins (Untouchable)
    public static function headSA(int $user_id)
    {
        return $user_id === 1;
    }

    public static function userIsPTA()
    {
        return in_array(Auth::user()->user_type, self::getPTA());
    }

    public static function userIsMyChild($student_id, $parent_id)
    {
        $data = ['user_id' => $student_id, 'my_parent_id' =>$parent_id];
        return StudentRecord::where($data)->exists();
    }

    public static function getSRByUserID($user_id)
    {
        return StudentRecord::where('user_id', $user_id)->first();
    }

    public static function getPTA()
    {
        return ['super_admin', 'admin', 'teacher', 'parent'];
    }

    /*public static function filesToUpload($programme)
    {
        return ['birth_cert', 'passport',  'neco_cert', 'waec_cert', 'ref1', 'ref2'];
    }*/

    public static function getPublicUploadPath()
    {
        return 'uploads/';
    }

    public static function getUserUploadPath()
    {
        return 'uploads/'.date('Y').'/'.date('m').'/'.date('d').'/';
    }

    public static function getUploadPath($user_type)
    {
        return 'uploads/'.$user_type.'/';
    }

    public static function getFileMetaData($file)
    {
        //$dataFile['name'] = $file->getClientOriginalName();
        $dataFile['ext'] = $file->getClientOriginalExtension();
        $dataFile['type'] = $file->getClientMimeType();
        $dataFile['size'] = self::formatBytes($file->getSize());
        return $dataFile;
    }

    public static function generateUserCode()
    {
        return substr(uniqid(mt_rand()), -7, 7);
    }

    public static function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

    public static function getSetting($type)
    {
        return Setting::where('type', $type)->first()->description;
    }

    public static function getCurrentSession()
    {
        return self::getSetting('current_session');
    }

    public static function getNextSession()
    {
        $oy = self::getCurrentSession();
        $old_yr = explode('-', $oy);
        return ++$old_yr[0].'-'.++$old_yr[1];
    }

    public static function getSystemName()
    {
        return self::getSetting('system_name');
    }

    public static function findMyChildren($parent_id)
    {
        return StudentRecord::where('my_parent_id', $parent_id)->with(['user', 'my_class'])->get();
    }

    public static function findTeacherSubjects($teacher_id)
    {
        return Subject::where('teacher_id', $teacher_id)->with('my_class')->get();
    }

    public static function findStudentRecord($user_id)
    {
        return StudentRecord::where('user_id', $user_id)->first();
    }



    public static function json($msg, $ok = TRUE, $arr = [])
    {
        return $arr ? response()->json($arr) : response()->json(['ok' => $ok, 'msg' => $msg]);
    }

    public static function jsonStoreOk()
    {
        return self::json(__('msg.store_ok'));
    }

    public static function jsonUpdateOk()
    {
        return self::json(__('msg.update_ok'));
    }

    public static function storeOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.store_ok'));
    }

    public static function deleteOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.del_ok'));
    }

    public static function updateOk($routeName)
    {
        return self::goWithSuccess($routeName, __('msg.update_ok'));
    }

    public static function goToRoute($goto, $status = 302, $headers = [], $secure = null)
    {
        $data = [];
        $to = (is_array($goto) ? $goto[0] : $goto) ?: 'dashboard';
        if(is_array($goto)){
            array_shift($goto);
            $data = $goto;
        }
        return app('redirect')->to(route($to, $data), $status, $headers, $secure);
    }

    public static function goWithDanger($to = 'dashboard', $msg = NULL)
    {
        $msg = $msg ? $msg : __('msg.rnf');
        return self::goToRoute($to)->with('flash_danger', $msg);
    }

    public static function goWithSuccess($to, $msg)
    {
        return self::goToRoute($to)->with('flash_success', $msg);
    }

    public static function getDaysOfTheWeek()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }


    public static function getUserRole(){
         $roles = \App\Models\Role::where('id', Auth::user()->role_id)->get();
         return $roles[0];
    }

    public static function menuAccess(){
        if(Auth::user()->user_type != 'super_admin'){
            $menu_ids = explode(",",Qs::getUserRole()->main_menu_ids);
            return $main_menu_names = MainMenu::select('id','title','icon')->where('status',1)->orderBy('orderwise')->whereIn('id',$menu_ids)->get();
        }
        else{
            return $main_menu_names = MainMenu::select('id','title','icon')->where('status',1)->orderBy('orderwise')->get();
        }

    }

    public static function subMenuAccess(){

        if(Auth::user()->user_type != 'super_admin'){

             $menu_ids = explode(",",Qs::getUserRole()->sub_menu_ids);
             return $sub_menu_names = SubMenu::select('id','name','m_controller_name','m_parent_code')->where('status',1)->where('page_type',1)->whereIn('id',$menu_ids)->get();
        }
        else{
            return $sub_menu_names = SubMenu::select('id','name','m_controller_name','m_parent_code')->where('status',1)->where('page_type',1)->get();
        }

    }
    public static function crudRights(){
        $req_uri = substr($_SERVER['REQUEST_URI'],1);
         $page_id = \App\Models\SubMenu::where('m_controller_name',$req_uri)->value('id');
        $crud_rights = explode(',',Qs::getUserRole()->crud_rights);
       // print_r($crud_rights) ;die;

    }


    public static function dateFormat($date){

        return $newDate = date("d-M-Y", strtotime($date));
    }

    public static function dateFormat2($date){

        return date('h:i:s a', strtotime($date));
    }


    public static function curl($arr,$page){


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,env('DOC_URL').$page);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query($arr));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        return $server_output;
    }




    public static function finAuthorityToUser(){

        return ['cfo','super_admin'];
    }

    public static function storelogs($request){

        $data = array(
            'user_id'=> Auth::user()->id,
            'url'=> $request->url(),
            'ip_address'=>$request->session()->get('ip_address'),
            'latitude'=>$request->session()->get('latitude'),
            'longitude'=>$request->session()->get('longitude'),
            'timestamp'=>date('d-m-y h:i:s'),
            'type'=>'in');
        ActivityLogs::create($data);

    }


    public static function displayPrintButtonInView($param1){
        ?>
        <button type="button" class="btn btn-sm btn-info btn-rounded" onclick="printView('<?php echo $param1?>')">
            <i class="icon-printer"></i>&nbsp; Print
        </button>
        <?php
    }


    public static function statusLabel($status){

        $statusAr = [1 => "Active", "Deleted"];
        $title = $statusAr[$status];
        $class = [1 => "success", 2 => "secondary", 3 => "primary", 4 => "success"];
        return '<span class="badge badge-pill badge-' . ($class[$status]) . '">' . $title . '</span>';

    }

    public static function paidStatus($id,$inv_amount)
    {

        $data = FinGeneralLedger::where('invoices_id',$id)->sum('debit');
        if($data >= $inv_amount){
            return '<span class="badge badge-pill badge-success">Paid</span>';
        }
        else{
            return '<span class="badge badge-pill badge-warning">Unpaid</span>';
        }

    }
    public static function installmentDateExpiry($date){

        if( date('Y-m-d') > $date){
            return '<span class="badge expBadge badge-pill badge-danger"> '.self::dateFormat($date).'</span>';

        }
        else{

            return '<span class="badge expBadge badge-pill badge-success"> '.self::dateFormat($date).'</span>';


        }


    }



    static function convertNumberToWord($num = false) {
        $num = str_replace(array(',', ' '), '', trim($num));
        if (!$num) {
            return false;
        }
        $num = (int) $num;
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
            } else {
                $tens = (int) ($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . ( ( $levels && (int) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }


//    static function monthDifference($date1,$date2){
//
//
//        $timeStart = strtotime($date2);
//        $timeEnd = strtotime($date1);
//        $numMonths = 1 + (date("Y",$timeEnd)-date("Y",$timeStart))*12;
//
//        $numMonths += date("m",$timeEnd)-date("m",$timeStart);
//
//        return $numMonths;
//    }


    static function DateDifference($start_date,$end_date){
        $begin = new DateTime($start_date);
        $end = new DateTime($end_date);
        return  new DatePeriod($begin, new DateInterval('P1D'), $end);
    }

    public static function reportHeader($datef,$datet){?>

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <h4>Generated from  <?php echo Qs::dateFormat($datef) ?> to <?php echo Qs::dateFormat($datet) ?></h4>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
                <h4><?php echo env('APP_NAME')?></h4>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <h4>Date : <?php echo static::dateFormat(date('d-m-Y')); ?></h4>
            </div>
        </div>
        <?php
    }


    public static function getInstallmentDate($date){

       echo date('d',strtotime($date))."-".date('M-Y');

    }



    public static function trialBalance(){

        $to_date = date('Y-m-d');
        $from_date =  "2020-01-01";
        $assets = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $to_date)
            ->where('fca_l5.parent_code','like','01%')
            ->where('voucher_status','3')

            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))

            ->groupBy('fca_l5.code')
            ->get();

        $liabilities = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $to_date)
            ->where('fca_l5.parent_code','like','02%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();

        $equity = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date", '<=', $to_date)
            ->where('fca_l5.parent_code','like','03%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();
        $expense = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($from_date, $to_date), "and")
            ->where('fca_l5.parent_code','like','04%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();
        $income = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereBetween("gl.txn_date", array($from_date, $to_date), "and")
            ->where('fca_l5.parent_code','like','05%')
            ->where('voucher_status','3')
            ->select(DB::raw("
                    fca_l5.code as 'L5_Code', fca_l5.title as 'L5_Title',
                   gl.id,SUM(gld.debit) as debit,SUM(gld.credit) as credit"))
            ->groupBy('fca_l5.code')
            ->get();

        $expense_close = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date",'<',$from_date)
            ->where('fca_l5.parent_code','like','04%')
            ->where('voucher_status','3')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $income_close = DB::table("fin_general_ledgers as gl")
            ->join("fin_general_ledger_details as gld", "gld.fin_gen_id", "=", "gl.id")
            ->join("fin_chart_of_accounts as fca_l5", "fca_l5.id", "=", "gld.coa_id")
            ->whereDate("gl.txn_date",'<',$from_date)
            ->where('fca_l5.parent_code','like','05%')
            ->where('voucher_status','3')
            ->select(DB::raw("SUM(gld.debit) as debit,SUM(gld.credit) as credit"));

        $expense_prev = $expense_close->sum('gld.debit')- $expense_close->sum('gld.credit');
        $income_prev  = $income_close->sum('gld.debit') - $income_close->sum('gld.credit');

        $ret = ($income_prev) + ($expense_prev);
        if ($ret < 0) {
            $retained_credit = $ret;
        } else {
            $retained_debit = $ret;
        }
        $total_debit_assets = 0;
        $total_credit_assets = 0;
        $dedit_assets = 0;
        $dedit_liability = 0;
        $dedit_equity = 0;
        $retained_credit = 0;
        $credit_expense = 0;
        $dedit_income = 0;
        $total_debit_liabilitiy = 0;
        $total_credit_liabilitiy = 0;
        $total_debit_equity = 0;
        $total_credit_equity = 0;
        $total_debit_expense = 0;
        $total_credit_expense = 0;
        $total_debit_income = 0;
        $total_credit_income = 0;
        $credit_assets = 0;
        $credit_liabality = 0;
        $credit_equity = 0;


        foreach ($assets as $val) {
            if (($val->debit - $val->credit) < 0) {
                $credit_assets = $val->debit - $val->credit;
            } else {
                $dedit_assets = $val->debit - $val->credit;
            }
            $total_debit_assets += $dedit_assets;
            $total_credit_assets += $credit_assets;
            $credit_assets = 0;
            $dedit_assets = 0;
        }



        foreach ($liabilities as $val2) {
            if (($val2->debit - $val2->credit) < 0) {
                $credit_liabality = $val2->debit - $val2->credit;
            } else {
                $dedit_liability = $val2->debit - $val2->credit;
            }
            $total_debit_liabilitiy += $dedit_liability;
            $total_credit_liabilitiy += $credit_liabality;
            $dedit_liability = 0;
            $credit_liabality = 0;
        }



        foreach ($equity as $val3) {
            if (($val2->debit - $val2->credit) < 0) {
                $credit_equity = $val3->debit - $val3->credit;
            } else {
                $dedit_equity = $val3->debit - $val3->credit;
            }
            $total_debit_equity += $dedit_equity;
            $total_credit_equity += $credit_equity;
            $credit_equity = 0;
            $dedit_equity = 0;
        }

        foreach ($expense as $val4) {
            if (($val4->debit - $val4->credit) < 0) {
                $credit_expense = $val4->debit - $val4->credit;
            } else {
                $dedit_expense = $val4->debit - $val4->credit;
            }
            $total_debit_expense += $dedit_expense;
            $total_credit_expense += $credit_expense;
            $credit_expense = 0;
            $dedit_expense = 0;
        }


        foreach ($income as $val5) {
            if (($val5->debit - $val5->credit) < 0) {
                $credit_income = $val5->debit - $val5->credit;
            } else {
                $dedit_income = $val5->debit - $val5->credit;
            }
            $total_debit_income += $dedit_income;
            $total_credit_income += $credit_income;
            $dedit_income = 0;
            $credit_income = 0;
        }

        $grand_debit = $retained_debit + $total_debit_assets + $total_debit_liabilitiy + $total_debit_equity + $total_debit_expense + $total_debit_income;
        $grand_credit = abs($retained_credit) + abs($total_credit_assets) + abs($total_credit_liabilitiy) + abs($total_credit_equity) + abs($total_credit_expense) + abs($total_credit_income);

        return array('debit'=>$grand_debit,'credit'=>$grand_credit);
    }


    public static function invoice_status($status) {
        $statusAr = [1 => "Pending", "Approved"];
        $title = $statusAr[$status];

        $class = [1 => "warning", 2 => "success"];
        return '<span class="badge badge-pill badge-' . ($class[$status]) . '">' . $title . '</span>';
    }


}
