

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h3>Vendor Form</h3>
            </div>


        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <input type="hidden" value="1" name="vendor_form">
            <label>Name</label>
            <input type="text" name="name" id="name" value="" required class="form-control" />
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <label>Ntn</label>
            <input type="text" name="ntn" id="ntn" value="" required class="form-control" />
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <label>Contact</label>
            <input type="text" name="contact" id="contact" value="" required class="form-control" />
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <label>City</label>
            <select class="form-control" name="city_id" id="city_id" required>
                @foreach($cities as $val)
                    <option value="{{$val->id}}">{{$val->name}}</option>
                @endforeach

            </select>
        </div>
        <br><br><br><br>
{{--        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">--}}
{{--            <label>Chart of Accounts</label>--}}
{{--            <select class="form-control" name="coa_id" id="coa_id" required>--}}
{{--                <option value="">Select</option>--}}
{{--                @foreach($accounts as $val)--}}
{{--                    <option value="{{$val->id}}">{{$val->code.' - '.$val->title}}</option>--}}
{{--                @endforeach--}}

{{--            </select>--}}
{{--        </div>--}}
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <label> Address </label>
            <textarea class="form-control" name="address" required></textarea>
        </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h3> Invoice Form</h3>
            </div>

<script>
    $("#coa_id").select2();
    $("#city_id").select2();
</script>
