@extends('layouts.master')
@section('page_title', 'Asset Depreciation Report Form')
@section('content')
    <style>
        tr td ,th{padding: 3px !important;}
        .dataTable > thead > tr > th[class*="sort"]:before,
        .dataTable > thead > tr > th[class*="sort"]:after {
            content: "" !important;
        }

    </style>
    <div class="card card-default">
        <div class="card-body">
            <form method="POST" action="{{ route('reports.assetDepreciationReportDetail') }}"  role="form" enctype="multipart/form-data">
                @csrf
                <div class="box box-info padding-1">
                    <div class="box-body">
                        <div class="row">
                            <?php //print_r($items[0]);die; ?>
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                {{ Form::label('Category List :') }}
                                <select required name="category" id="category" required class="form-control select-search">
                                    <option value="all">All</option>
                                    @foreach($items as $row)
                                        <option @if($category == $row->id) selected @endif value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                {{ Form::label('Year :') }}
                                <select required name="year" id="category" required class="form-control select-search">
                                    <option value="all">All</option>
                                    @foreach(\App\Models\Setting::where('type','current_session')->get() as $row)
                                        <option @if($year == $row->description) selected @endif value="{{$row->description}}">{{$row->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                <div class="box-footer mt20">
                                    <br>
                                    <button type="submit" class="btn btn-primary"><i class="icon-chevron-right"></i>Generate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                </div>
            </form>
            @if($category != '')

                <div class="table-responsive">
                    <table class="table datatable-button-html5-columns">
                       <thead>
                           <tr class="datatable-header center" style="padding: 4px;">
                           <tr>
                               <th colspan="6"></th>
                               <th class="text-center" colspan="3"><---- <strong> COST </strong>----></th>
                               <th class="text-center" colspan="13"><strong><---- ACCUMULATED DEPRECIATION</strong>----></th>
                               <th colspan="2"></th>
                           </tr>
                           <tr>
                               <th>Sr.#</th>
                               <th>Category</th>
                               <th>Assets</th>
                               <th>Code</th>
                               <th>Year</th>
                               <th class="text-center">Asset Amount</th>
                               <th class="text-center">Impairment (-)</th>
                               <th class="text-center">total</th>
                               <th>Dep%</th>
                               <th class="text-right">%value</th>
                               <th class="text-right">Jan</th>
                               <th class="text-right">Feb</th>
                               <th class="text-right">Mar</th>
                               <th class="text-right">April</th>
                               <th class="text-right">May</th>
                               <th class="text-right">June</th>
                               <th class="text-right">July</th>
                               <th class="text-right">Aug</th>
                               <th class="text-right">Sep</th>
                               <th class="text-right">Oct</th>
                               <th class="text-right">Nov</th>
                               <th class="text-right">Dec</th>
                               <th class="text-right">Dep Accum.</th>
                               <th class="text-right">Nrv</th>

                        </tr>

                        </thead>
                        <tbody>
                        <?php
                            $counter =0;

                            $amount = 0;
                            $current_session = Qs::getCurrentSession();
                            $dep_amount_total = 0;
                            $imp_amount_total = 0;
                            $total = 0;
                            $percent_amount_total = 0;
                            $jan_total = 0;
                            $feb_total = 0;
                            $mar_total = 0;
                            $april_total = 0;
                            $may_total = 0;
                            $june_total = 0;
                            $july_total = 0;
                            $aug_total = 0;
                            $sep_total = 0;
                            $oct_total = 0;
                            $nov_total = 0;
                            $dec_total = 0;
                            $nrv_total = 0;
                            $dep_acc_total = 0;
                            $prev_value_item =  null;
                            $prev_value_subitem =  null;
                            ?>

                            @foreach($detail as $valDetail)
                                @if(\App\Models\Items::where('parent_id',$valDetail->id)->count() > 0)

                                    <?php
                                        if($year == 'all'):

                                        $detail2 =DB::table('asset_depreciation')
                                        ->join('items', 'items.id', '=', 'asset_depreciation.item_id')
                                        ->orderBy('asset_depreciation.item_id')
                                        ->where('items.parent_id',$valDetail->id)
                                        ->orderBy('asset_depreciation.year')
                                        ->get();

                                        else:

                                        $detail2 =DB::table('asset_depreciation')
                                            ->join('items', 'items.id', '=', 'asset_depreciation.item_id')
                                            ->orderBy('asset_depreciation.item_id')
                                            ->where('items.parent_id',$valDetail->id)
                                            ->where('asset_depreciation.year',$year)
                                            ->orderBy('asset_depreciation.year')
                                            ->get();
                                        endif;

                                    ?>
                                    @foreach($detail2 as $val)

                                            <?php

                                             $nrv = $val->total - ($val->jan + $val->feb + $val->mar +$val->apr + $val->may
                                                     + $val->june + $val->july + $val->aug + $val->sept + $val->oct + $val->nov + $val->dec);
                                            $dep_accum = ($val->jan + $val->feb + $val->mar +$val->apr + $val->may
                                                     + $val->june + $val->july + $val->aug + $val->sept + $val->oct + $val->nov + $val->dec);
                                            $dep_amount_total+=$val->dep_amount;
                                            $imp_amount_total+=$val->impairment;
                                            $total+=$val->total;
                                            $percent_amount_total+= $val->percent_of_amount;
                                            $jan_total += $val->jan;
                                            $feb_total += $val->feb;
                                            $mar_total += $val->mar;
                                            $april_total += $val->apr;
                                            $may_total += $val->apr;
                                            $june_total += $val->june;
                                            $july_total += $val->july;
                                            $aug_total += $val->aug;
                                            $sep_total += $val->sept;
                                            $oct_total += $val->oct;
                                            $nov_total += $val->nov;
                                            $dec_total += $val->dec;
                                            $nrv_total += $nrv;
                                            $dep_acc_total+= $dep_accum;
                                            $item_name = '';
                                            $subitem_name = '';
                                            $hide='';
                                            if($prev_value_item != $valDetail->id):
                                                $item_name = $valDetail->name;


                                            endif;
                                            if($prev_value_subitem != $val->item_id):
                                                $subitem_name = $val->name;

                                            endif;

                                            if($item_name != ''){
                                                $counter1 = ++$counter;
                                            }
                                            else{
                                                $counter1 = "<span style='color:white'>". $counter."</span>";
                                            }

                                            ?>
                                        <tr>
                                            <td class="text-center"><?php echo $counter1; ?></td>
                                            <td>
                                                {{$item_name}}
                                            </td>
                                            <td>
                                                {{$subitem_name}}
                                            </td>
                                            <td>AGFL - {{$val->code}}</td>
                                            <th>{{date("Y", strtotime($val->asset_date))}}</th>
                                            <td class="text-right">{{number_format($val->dep_amount)}}</td>
                                            <td class="text-right">{{number_format($val->impairment)}}</td>
                                            <td class="text-right">{{number_format($val->total)}}</td>
                                            <td>{{$val->dep_percentage}}%</td>
                                            <td class="text-right">{{number_format($val->percent_of_amount)}}</td>

                                            <td class="text-right">{{number_format($val->jan)}}</td>
                                            <td class="text-right">{{number_format($val->feb)}}</td>
                                            <td class="text-right">{{number_format($val->mar)}}</td>
                                            <td class="text-right">{{number_format($val->apr)}}</td>
                                            <td class="text-right">{{number_format($val->may)}}</td>
                                            <td class="text-right">{{number_format($val->june)}}</td>
                                            <td class="text-right">{{number_format($val->july)}}</td>
                                            <td class="text-right">{{number_format($val->aug)}}</td>
                                            <td class="text-right">{{number_format($val->sept)}}</td>
                                            <td class="text-right">{{number_format($val->oct)}}</td>
                                            <td class="text-right">{{number_format($val->nov)}}</td>
                                            <td class="text-right">{{number_format($val->dec)}}</td>
                                            <td class="text-right">{{number_format($dep_accum)}}</td>
                                            <td class="text-right">{{number_format($nrv)}}</td>


                                        </tr>
                                            <?php
                                                $prev_value_item = $valDetail->id;
                                                $prev_value_subitem = $val->item_id;
                                                ?>
                                    @endforeach
                                @endif

                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right" colspan="5"><strong>TOTAL</strong></td>
                                <td class="text-right" ><strong>{{number_format($dep_amount_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($imp_amount_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($total)}}</strong></td>
                                <td colspan="2" class="text-right"><strong>{{number_format($percent_amount_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($jan_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($feb_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($mar_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($april_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($may_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($june_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($july_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($aug_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($sep_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($oct_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($nov_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($dec_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($dep_acc_total)}}</strong></td>
                                <td class="text-right"><strong>{{number_format($nrv_total)}}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <br><br>

            @endif
        </div>
    </div>

@endsection
