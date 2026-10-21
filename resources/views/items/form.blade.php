<div class="box box-info padding-1">
    <div class="box-body">
        <div class="card-body">
           <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Item Name</label>
                    <input type="text" name="name" id="name" value="" required class="form-control" />
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Code</label>
                    <input type="text" name="code" id="code" value="" required class="form-control" />
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
