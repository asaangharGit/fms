<div class="box box-info padding-1">
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                {{ Form::label('Bank') }}
                <select class="form-control"  id="bank_id" name="bank_id">
                    @foreach($finBanks as $value)
                        <option value="{{$value->id}}">{{$value->bank_name}}</option>
                    @endforeach
                </select>
                {!! $errors->first('bank_id', '<div class="invalid-feedback">:message</p>') !!}
            </div>
            <div class="col-md-3">
                {{ Form::label('Chequebook Name') }}
                {{ Form::text('chequebook_name', $finCheckbook->chequebook_name, ['class' => 'form-control' . ($errors->has('chequebook_name') ? ' is-invalid' : ''), 'placeholder' => 'Chequebook Name']) }}
                {!! $errors->first('chequebook_name', '<div class="invalid-feedback">:message</p>') !!}

            </div>
            <div class="col-md-3">
                {{ Form::label('cheque number start') }}
                {{ Form::text('checknum_start', $finCheckbook->checknum_start, ['class' => 'form-control' . ($errors->has('checknum_start') ? ' is-invalid' : ''), 'placeholder' => 'Checknum Start']) }}
                {!! $errors->first('checknum_start', '<div class="invalid-feedback">:message</p>') !!}

            </div>
            <div class="col-md-3">

                {{ Form::label('cheque number end') }}
                {{ Form::text('checknum_end', $finCheckbook->checknum_end, ['class' => 'form-control' . ($errors->has('checknum_end') ? ' is-invalid' : ''), 'placeholder' => 'Checknum End']) }}
                {!! $errors->first('checknum_end', '<div class="invalid-feedback">:message</p>') !!}

            </div>
        </div>
    </div>
    <br>
    <div class="box-footer mt20 text-right">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>

    </div>

<Script>
    $("#bank_id").select2();
</Script>