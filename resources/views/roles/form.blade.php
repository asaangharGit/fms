<style>
    .grid1{
        border-radius: 10px;
        padding: 10px;
        border: 1px solid lightskyblue;

    }
    .panel-primary{
        padding: 10px;
    }
</style>
<div class="box box-info padding-1">
    <div class="box-body">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="col-12">
                    <div class="card">
                        <div class="card-body" id="PrintEmployeeList">
                           <div class="row">

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label class="sf-label pointer">Role Name</label>
                                    <input type="text" required name="role_name" id="role_name" class="form-control">
                                </div>

                               <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                   <br>
                                   <label class="sf-label pointer"><strong>Hide Confidentiality</strong></label>
                                    &nbsp; &nbsp;
                                    <input type="checkbox" checked name="hide_confidentiality" id="hide_confidentiality" >
                                </div>
                            </div>
                            <br>


                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <?php
                                    $MainMenuTitles = DB::table('main_menus')->select(['id','main_menu_id'])->where([['menu_type','=',1],['status', '=', 1]])->orderBy('id')->get();

                                    $counter = 1;
                                    foreach($MainMenuTitles as $row){ ?>
                                    <div class="panel panel-primary">
                                        <div class="panel-heading grid1">
                                            <h3 class="panel-title"><?php echo $row->main_menu_id;?>
                                                <input checked type="checkbox" name="main_menu[]" value="<?php echo $row->id;?>">
                                            </h3>
                                            <br>
                                            <span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>

                                        <div class="panel-body " id="Prilviges_<?php echo $row->main_menu_id;?>">
                                                <?php
                                                $MainMenuTitlesSub = DB::table('main_menus')->select(['main_menu_id','title','title_id','id'])->where([['main_menu_id','=',$row->main_menu_id],['menu_type','=',1],['status', '=', 1]])->orderBy('id', 'desc')->get();
                                            foreach($MainMenuTitlesSub as $row1){ ?>


                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

                                                    <ul class="nav">
                                                            <?php
                                                         $data = DB::table('sub_menus')->select(['name','id','page_type'])->where('m_parent_code','=',$row1->id)->get();
                                                        foreach($data as $dataValue){?>
                                                        <li class="pagesList">
                                                            <input name="sub_menu[]" checked type="checkbox" value="<?=$dataValue->id?>">
                                                            <strong> <?php echo $dataValue->name;?> {{($dataValue->page_type == 2)?'(inner)':''}}</strong>&nbsp;&nbsp;
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <p><b>Actions :</b></p>
                                                    <ul class="privilegesList nav">
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="view_<?=$dataValue->id?>" /> <strong>View</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="edit_<?=$dataValue->id?>" /> <strong>Edit</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="approve_<?=$dataValue->id?>" /> <strong>Approve</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="reject_<?=$dataValue->id?>" /> <strong>Reject</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="repost_<?=$dataValue->id?>" /> <strong>Repost</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="delete_<?=$dataValue->id?>" /> <strong>Delete</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="print_<?=$dataValue->id?>" /> <strong>Print</strong>&nbsp;&nbsp;</li>
                                                        <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights[]" value="export_<?=$dataValue->id?>" /> <strong>Export</strong>&nbsp;&nbsp;</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <br>
                                            <?php }?>
                                        </div></div></div>
                                        <?php }?>
                                    </div>

                                </div>
{{--                                <div class="row">--}}
{{--                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">--}}
{{--                                        <?php--}}
{{--                                        $MainMenuTitles = DB::table('main_menus')->select(['id','main_menu_id', 'menu_type'])->where([['menu_type','=',2],['status', '=', 1]])->groupBy('main_menu_id')->get();--}}

{{--                                        $counter = 1;--}}
{{--                                        foreach($MainMenuTitles as $row){ ?>--}}

{{--                                        <div class="panel panel-primary">--}}
{{--                                            <div class="panel-heading">--}}
{{--                                                <h3 class="panel-title"><?php echo $row->main_menu_id;?>--}}
{{--                                                    <input checked type="checkbox" id="<?php echo $row->main_menu_id;?>_master" name="main_modules[]" value="<?php echo $row->id;?>">--}}
{{--                                                </h3>--}}
{{--                                                <br>--}}
{{--                                                <span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>--}}
{{--                                                <div class="panel-body" id="Prilviges_master_<?php echo $row->main_menu_id;?>">--}}
{{--                                                        <?php--}}
{{--                                                        $MainMenuTitlesSub = DB::table('main_menus')->select(['main_menu_id','title','title_id','id'])->where([['main_menu_id','=',$row->main_menu_id],['menu_type','=',2],['status', '=', 1]])->orderBy('id', 'desc')->get();--}}
{{--                                                    foreach($MainMenuTitlesSub as $row1){ ?>--}}

{{--                                                    <div class="row">--}}
{{--                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">--}}
{{--                                                            <label style="text-decoration: underline;color:royalblue;"><?php echo $row1->title; ?> :</label>--}}
{{--                                                            <input type="hidden" name="menu_title_<?php echo $row->id;?>[]" value="<?php echo $row1->title_id; ?>" />--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="row">--}}
{{--                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                            <p><b>Pages / Screens :</b></p>--}}
{{--                                                            <ul class="nav">--}}
{{--                                                                    <?php--}}
{{--                                                                    $data = DB::table('men')->select(['name','id'])->where([['m_parent_code','=',$row1->id]])->get();--}}

{{--                                                                foreach($data as $dataValue){?>--}}
{{--                                                                <li>--}}
{{--                                                                    <input name="sub_menu_<?php echo $row1->title_id; ?>[]" checked type="checkbox" value="<?=$dataValue->id?>">--}}
{{--                                                                    <strong> <?php echo $dataValue->name;?></strong>&nbsp;&nbsp;--}}
{{--                                                                </li>--}}
{{--                                                                <?php } ?>--}}
{{--                                                            </ul>--}}
{{--                                                        </div>--}}
{{--                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">--}}
{{--                                                            <p><b>Actions :</b></p>--}}
{{--                                                            <ul class="privilegesList nav">--}}
{{--                                                                <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights_<?php echo $row1->title_id; ?>[]" value="edit" /> <strong>Edit</strong>&nbsp;&nbsp;</li>--}}
{{--                                                                <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights_<?php echo $row1->title_id; ?>[]" value="delete" /> <strong>Delete</strong>&nbsp;&nbsp;</li>--}}
{{--                                                                <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights_<?php echo $row1->title_id; ?>[]" value="print" /> <strong>Print</strong>&nbsp;&nbsp;</li>--}}
{{--                                                                <li class="pagesList"><input type="checkbox" checked="checked" name="crud_rights_<?php echo $row1->title_id; ?>[]" value="export" /> <strong>Export</strong>&nbsp;&nbsp;</li>--}}
{{--                                                            </ul>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="lineHeight">&nbsp;</div>--}}
{{--                                                    <?php }?>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

{{--                                        </div>--}}
{{--                                        <?php }?>--}}
{{--                                    </div>--}}

{{--                                </div>--}}
{{--                                <br>--}}
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>



                        </div>
                    </div>

                    <?php echo Form::close();?>
                    <div class="text-right"><span class="regionError" style="font-size:18px;"></span></div>

                </div>

            </div>

</div>









</div>
</div>
