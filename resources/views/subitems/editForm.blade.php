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
                            <option @if($detail[0]->parent_id == $value->id ) selected @endif value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label>Item Name</label>
                    <input type="text" name="name" id="name" value="@if($detail[0]->lev2) {{$detail[0]->lev2}} @endif" required class="form-control" />
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
