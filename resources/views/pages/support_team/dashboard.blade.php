@extends('layouts.master')
@section('page_title', 'My Dashboard')
@section('content')
<style>
.jsbank{background: #e35205; }
.fbl{ background:#046771;}
.budget{background: red;}
.card{border-radius: 10px;}
</style>
       <div class="row">
           <div class="loader"></div>

           <div class="col-md-3">
               <div class="row">
                   <div class="col-md-12"><h1>Trial Balance</h1></div>

               </div>
               <div class="card card-body bg-success-400 has-bg-image">
                   <div class="media">
                       <div class="mr-3 align-self-center">
                           <i class="icon-coin-dollar icon-3x opacity-75"></i>
                       </div>
                       <div class="media-body">
                           <h5 class="mb-0" id="trial_d">0</h5>
                           <h5 class="mb-0" id="trial_c">0</h5>
                           <h5 class="mb-0" id="trial_t">0</h5>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-md-3">
               <div class="row">
                   <div class="col-md-12">
                       <h1>JS Bank Balance</h1>
                   </div>

               </div>
               <div class="card card-body has-bg-image jsbank">
                   <div class="media">
                       <div class="media-body">
                           <h5 class="mb-0" id="js_d">0</h5>
                           <h5 class="mb-0" id="js_c">0</h5>
                           <h5 class="mb-0" id="js_t">0</h5>
                       </div>
                       <div class="ml-3 align-self-center">
                           <i class="icon-coin-dollar icon-3x opacity-75"></i>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-md-3">

           </div>
       </div>
           <div class="row">
           <div class="col-md-3">
               <div class="row">
                   <div class="col-md-12"><h1>FBL (P)</h1></div>

               </div>
               <div class="card card-body fbl has-bg-image">
                   <div class="media">
                       <div class="media-body">
                           <h5 class="mb-0" id="fblp_d">0</h5>
                           <h5 class="mb-0" id="fblp_c">0</h5>
                           <h5 class="mb-0" id="fblp_t">0</h5>
                       </div>
                       <div class="ml-3 align-self-center">
                           <i class="icon-coin-dollar icon-3x opacity-75"></i>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-md-3">
               <div class="row">

                   <div class="col-md-12"><h1>FBL (E)</h1></div>

               </div>
               <div class="card card-body fbl has-bg-image">
                   <div class="media">
                       <div class="media-body">
                           <h5 class="mb-0" id="fble_d">0</h5>
                           <h5 class="mb-0" id="fble_c">0</h5>
                           <h5 class="mb-0" id="fble_t">0</h5>
                       </div>
                       <div class="ml-3 align-self-center">
                           <i class="icon-coin-dollar icon-3x opacity-75"></i>
                       </div>
                   </div>
               </div>
           </div>
           </div>
           <div class="row">
           <div class="col-md-3">
               <div class="row">

                   <div class="col-md-12"><h1>FBL (D)</h1></div>

               </div>
               <div class="card card-body fbl has-bg-image">
                   <div class="media">
                       <div class="media-body">
                           <h5 class="mb-0" id="fbld_d">0</h5>
                           <h5 class="mb-0" id="fbld_c">0</h5>
                           <h5 class="mb-0" id="fbld_t">0</h5>
                       </div>
                       <div class="ml-3 align-self-center">
                           <i class="icon-coin-dollar icon-3x opacity-75"></i>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-md-3">
               <div class="row">
                  
                   <div class="col-md-12"><h1>FBL (C)</h1></div>

               </div>
               <div class="card card-body fbl has-bg-image">
                   <div class="media">
                       <div class="media-body">
                           <h5 class="mb-0" id="fblc_d">0</h5>
                           <h5 class="mb-0" id="fblc_c">0</h5>
                           <h5 class="mb-0" id="fblc_t">0</h5>
                       </div>
                       <div class="ml-3 align-self-center">
                           <i class="icon-coin-dollar icon-3x opacity-75"></i>
                       </div>
                   </div>
               </div>
           </div>
       </div>

   <script>
       function dashboardData(){


           $.getJSON( '{{route('dashboardData')}}', function( json ) {
               console.log(json.trial_balance)

               $("#trial_d").html(json.trial_balance.trialDebit+"&nbsp;&nbsp;<span>Debit</span>");
               $("#trial_c").html(json.trial_balance.trialCredit+"&nbsp;&nbsp;<span>Credit</span>");
               $("#trial_t").html(json.trial_balance.trial_total+"&nbsp;&nbsp;<span>Total</span>");

               $("#js_d").html(json.js_bank.jsBankDebit+"&nbsp;&nbsp;<span>Debit</span>");
               $("#js_c").html(json.js_bank.jsBankCredit+"&nbsp;&nbsp;<span>Credit</span>");
               $("#js_t").html(json.js_bank.jsBankTotal+"&nbsp;&nbsp;<span>Total</span>");

               $("#fblp_d").html(json.fbl_p.fbl_pDebit+"&nbsp;&nbsp;<span>Debit</span>");
               $("#fblp_c").html(json.fbl_p.fbl_pCredit+"&nbsp;&nbsp;<span>Credit</span>");
               $("#fblp_t").html(json.fbl_p.fbl_pTotal+"&nbsp;&nbsp;<span>Total</span>");

               $("#fble_d").html(json.fbl_e.fbl_eDebit+"&nbsp;&nbsp;<span>Debit</span>");
               $("#fble_c").html(json.fbl_e.fbl_eCredit+"&nbsp;&nbsp;<span>Credit</span>");
               $("#fble_t").html(json.fbl_e.fbl_eTotal+"&nbsp;&nbsp;<span>Total</span>");

               $("#fbld_d").html(json.fbl_d.fbl_dDebit+"&nbsp;&nbsp;<span>Debit</span>");
               $("#fbld_c").html(json.fbl_d.fbl_dCredit+"&nbsp;&nbsp;<span>Credit</span>");
               $("#fbld_t").html(json.fbl_d.fbl_dTotal+"&nbsp;&nbsp;<span>Total</span>");

               $("#fblc_d").html(json.fbl_c.fbl_cDebit+"&nbsp;&nbsp;<span>Debit</span>");
               $("#fblc_c").html(json.fbl_c.fbl_cCredit+"&nbsp;&nbsp;<span>Credit</span>");
               $("#fblc_t").html(json.fbl_c.fbl_cTotal+"&nbsp;&nbsp;<span>Total</span>");

               $("#budget_d").html(json.income+"&nbsp;&nbsp;<span>Income</span>");
               $("#budget_c").html(json.expense+"&nbsp;&nbsp;<span>Expense</span>");
               $("#budget_t").html(json.income-json.expense+"&nbsp;&nbsp;<span>Total</span>");

           });
       }


       dashboardData()
   </script>

    @endsection
