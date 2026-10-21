<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            {{ Form::label('title') }}
            {{ Form::text('title', $finChartOfAccount->title, ['class' => 'form-control' . ($errors->has('title') ? ' is-invalid' : ''), 'placeholder' => 'Title']) }}
            {!! $errors->first('title', '<div class="invalid-feedback">:message</p>') !!}
        </div>
{{--        <div class="form-group">--}}
{{--            {{ Form::label('code') }}--}}
{{--            {{ Form::text('code', $finChartOfAccount->code, ['class' => 'form-control' . ($errors->has('code') ? ' is-invalid' : ''), 'placeholder' => 'Code']) }}--}}
{{--            {!! $errors->first('code', '<div class="invalid-feedback">:message</p>') !!}--}}
{{--        </div>--}}


    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
