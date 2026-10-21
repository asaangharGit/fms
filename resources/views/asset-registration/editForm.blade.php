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
                            <option @if($detail[0]->item_id == $value->id ) selected @endif value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Amount</label>
                    <input type="text" name="amount" id="amount" value="{{$detail[0]->amount }}" required class="form-control" />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Percent</label>
                    <input type="text" name="percent" id="percent" max="100" value="{{$detail[0]->percent}}" required class="form-control" />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Date</label>
                    <input type="created_on" name="created_on" id="date" value="{{$detail[0]->created_on}}" required class="form-control" />
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>BPV</label>
                    <select required class="form-control select-search" name="fin_gen_id" id="fin_gen_id">
                        <option value="">Select</option>
                        @foreach($bpv as $value)
                            <option @if($value->id == $detail[0]->fin_gen_id) selected @endif value="{{$value->id}}">{{$value->details}}</option>
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
