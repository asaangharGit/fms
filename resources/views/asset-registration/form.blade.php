    <div class="box box-info padding-1">
    <div class="box-body">
        <div class="card-body">
           <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Item List</label>
                    <select required class="form-control select-search" name="item_id" id="item_id">
                        <option value="">Select</option>
                        @foreach($items as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Amount</label>
                    <input type="text" name="amount" id="amount" value="" required class="form-control" />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Percent</label>
                    <input type="text" name="percent" id="percent" value="" max="100" required class="form-control" />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Date</label>
                    <input required name="created_on" class="form-control" type="date" value="{{date("Y-m-d")}}" max="{{date("Y-m-d")}}">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>BPV</label>
                    <select required class="form-control select-search" name="fin_gen_id" id="fin_gen_id">
                        <option value="">Select</option>
                        @foreach($bpv as $value)
                            <option value="{{$value->id}}">{{$value->details}}</option>
                        @endforeach
                    </select>
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
