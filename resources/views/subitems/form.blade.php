<div class="box box-info padding-1">
    <div class="box-body">
        <div class="card-body">
           <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label>Item List</label>
                    <select required class="form-control select-search" name="item_id" id="item_id">
                        <option value="">Select</option>
                        @foreach($items as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <label>Code</label>
                    <input required class="form-control" readonly name="code" id="code">
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                    <label>Sub Item</label>
                   <input required class="form-control" name="sub_item" id="sub_item">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                    {{ Form::submit('Submit', ['class' => 'btn btn-sm btn-success']) }}
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $("#item_id").change(function(){
        var item_id = $("#item_id").val();
        var url = '<?php echo route("subitems.getItemCode")?>?item_id='+item_id;
        $.ajax({
            url:url,
            method:"GET",
            success: function(data){
                $("#code").val(data)
            }
        });
    });
</script>
