var base_url = $("meta[name=base_url]").attr("content");
var user_id = $("meta[name=user_id]").attr("content");
var csrf_token = $("meta[name=csrf-token]").attr("content");
var ip_address = $("meta[name=ip_address]").attr("content");
document.addEventListener('DOMContentLoaded', function() {
    $('.date-pick').datepicker();
});
function number_format(number,decimals,dec_point,thousands_sep) {

    number  = number*1;//makes sure `number` is numeric value
    var str = number.toFixed(decimals?decimals:0).toString().split('.');
    var parts = [];
    for ( var i=str[0].length; i>0; i-=3 ) {
        parts.unshift(str[0].substring(Math.max(0,i-3),i));
    }
    str[0] = parts.join(thousands_sep?thousands_sep:',');

    return str.join(dec_point?dec_point:'.');
}


var row=$("#vmsrows").val();

$('.add-row').click(function(){

    var counter = $(".count-vms-rows").length;

    $.get(base_url+"/finance/ledger/addMoreRowVoucher?counter="+counter, function(data, status){
       // console.log(data)
        $("#gl_rows").append(data);
        counter++
        $("#chartofaccount_"+counter).html(data);
        $("#chartofaccount_"+counter).select2();
    });

});

$('.add-row-edit').click(function(){

    var counter = $(".count-vms-rows").length;
    $.get(base_url+"/finance/ledger/addMoreRowEditVoucher?counter="+counter, function(data, status){
        console.log(data)
        $("#gl_rows").append(data);
        counter++
        $("#chartofaccount_"+counter).select2();
    });

});


function removeVmsRow(id){
    $(".gl_rows_"+id).remove()
}


//

function debit(){

    var granddebit = 0;
    var grandcredit = 0;
    $(".debit").each(function(){
      //  console.log($(this).val())
        granddebit+=parseInt($(this).val());
    });
    $(".credit").each(function(){
       // console.log($(this).val())
        grandcredit+=parseInt($(this).val());
    });

    $(".granddebit1").html(number_format(granddebit));
    if(granddebit != grandcredit){
        $(".submitbtn").attr("disabled","disabled")
        $("#vms-error").html("Debit / Credit is not matched...!")
    }else{
        $(".submitbtn").removeAttr("disabled")
        $("#vms-error").html('')
    }

    var invoice = $("#invoices_id").val();

    if(invoice) {

        var diff = $("#invoice_paid_amount").val()

        if (granddebit > diff) {
            $(".submitbtn").attr("disabled", "disabled")
            $("#vms-error").html("Invoice Amount exceed...!")
        } else {
            $(".submitbtn").removeAttr("disabled")
            $("#vms-error").html('')
        }
    }
}
function credit(){
    var grandcredit = 0;
    var granddebit = 0;
    $(".credit").each(function(){
   //     console.log($(this).val())
        grandcredit+=parseInt($(this).val());
    });
    $(".debit").each(function(){
     //   console.log($(this).val())
        granddebit+=parseInt($(this).val());
    });
    $(".grandcredit1").html(number_format(grandcredit));

    if(granddebit != grandcredit){
        $(".submitbtn").attr("disabled","disabled")
        $("#vms-error").html("Debit / Credit is not matched...!")
    }else{
        $(".submitbtn").removeAttr("disabled")
        $("#vms-error").html('')
    }

    var invoice = $("invoices_id").val();

    if(invoice) {
        var diff = $("#invoice_paid_amount").val()

        if (grandcredit > diff) {
            $(".submitbtn").attr("disabled", "disabled")
            $("#vms-error").html("Invoice Amount exceed...!")
        } else {
            $(".submitbtn").removeAttr("disabled")
            $("#vms-error").html('')
        }
    }
}
// $(".debit").keyup(function(){
//
//     var granddebit = 0;
//     var grandcredit = 0;
//     $(".debit").each(function(){
//         console.log($(this).val())
//         granddebit+=parseInt($(this).val());
//     });
//     $(".credit").each(function(){
//         console.log($(this).val())
//         grandcredit+=parseInt($(this).val());
//     });
//     $(".granddebit").html(number_format(granddebit));
//     if(granddebit != grandcredit){
//         $(".submitbtn").attr("disabled","disabled")
//         $("#vms-error").html("Debit / Credit is not matched...!")
//     }else{
//         $(".submitbtn").removeAttr("disabled")
//         $("#vms-error").html('')
//     }
// });
// $(".credit").keyup(function(){
//
//     var grandcredit = 0;
//     var granddebit = 0;
//     $(".credit").each(function(){
//         console.log($(this).val())
//         grandcredit+=parseInt($(this).val());
//     });
//     $(".debit").each(function(){
//         console.log($(this).val())
//         granddebit+=parseInt($(this).val());
//     });
//     $(".grandcredit").html(number_format(grandcredit));
//
//     if(granddebit != grandcredit){
//         $(".submitbtn").attr("disabled","disabled")
//         $("#vms-error").html("Debit / Credit is not matched...!")
//     }else{
//         $(".submitbtn").removeAttr("disabled")
//         $("#vms-error").html('')
//     }
// });

$(document).ready(function(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition,showError);
    } else {
       alert("Geolocation is not supported by this browser.");
    }

})

function showPosition(){}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("Error..! Allow your location.");
            window.location.replace(base_url+"/logout");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Error..! Location information is unavailable.");
            window.location.replace(base_url+"/logout");
            break;
        case error.TIMEOUT:
            alert("Error..! The request to get user location timed out.");
            window.location.replace(base_url+"/logout");
            break;
        case error.UNKNOWN_ERROR:
            alert("Error..! An unknown error occurred.");
            window.location.replace(base_url+"/logout");
            break;
    }
}

function welcome_letter(path){

    $("#welcome_letter").html('generating....')
    $.get(path, function(data){
        var obj = jQuery.parseJSON ( data );
        var errors = [1,2,3,4,5];
        if(errors.includes( obj.err )){
            alert(obj.msg)
            $("#welcome_letter").html('Welcome Letter')
        } else{
            var link = document.createElement("a");
            link.setAttribute('download', name);
            link.href = obj.msg;
            document.body.appendChild(link);
            link.click();
            link.remove();
            $("#welcome_letter").html('Welcome Letter')
        }
    });
}


function generateAcknowledgementLetter(path){

    $("#acknow_letter").html('generating....')
    $.get(path, function(data){
        var obj = jQuery.parseJSON ( data );
        var errors = [1,2,3,4,5];
        if(errors.includes( obj.err )){
            alert(obj.msg)
            $("#acknow_letter").html('Acknowledgement')
        } else{
            var link = document.createElement("a");
            link.setAttribute('download', name);
            link.href = obj.msg;
            document.body.appendChild(link);
            link.click();
            link.remove();
            $("#acknow_letter").html('Acknowledgement')
        }
    });
}


$('#show_all_activity').click(function(){

    if($(this).is(':checked'))
    {
        $("#date").attr('disabled','disabled')
    }
    else{

        $("#date").removeAttr('disabled')
    }
})


function getUserActivity(){

    if($('#show_all_activity').prop('checked')) {
        var show_all = 'yes';

    } else {
        var show_all = 'no';
    }
    $("#logs_area").html('')|
    $("#search_log").html('searching......')
    var date = $("#date").val();
    var user_id = $("#user").val();
    $("#loading").html('Loading.....')
    $.get(base_url+"/userPageActivity/?user_id="+user_id+'&date='+date+'&show_all='+show_all, function(data, status){

        $("#logs_area").html(data)
        $("#loading").fadeOut('slow')
        $("#search_log").html('<i class="icon-search4"></i> Search')
    });


}


function getLoginActivity(){

    if($('#show_all_login').prop('checked')) {
        var show_all = 'yes';

    } else {
        var show_all = 'no';
    }

    $("#search_log").html('searching......')
    var date = $("#date").val();
    var user_id = $("#user").val();
    $("#loading").html('Loading.....')
    $.get(base_url+"/loginPageActivity/?user_id="+user_id+'&date='+date+'&show_all='+show_all, function(data, status){

        $("#logs_area").html(data)
        $("#loading").fadeOut('slow')
        $("#search_log").html('<i class="icon-search4"></i> Search')
    });
}

function printView(param1) {


    var printContents = document.getElementById(param1).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31
        && (charCode < 48 || charCode > 57))
        return false;

    return true;
}


var tableToExcel = (function() {
    var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
        , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    return function(table, name) {
        if (!table.nodeType) table = document.getElementById(table)
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
        window.location.href = uri + base64(format(template, ctx))
    }
})()

function getCode(val){

    $("#code").val(val)
}
function getL4Code(val){
    $("#loading").html('Loading.....')
    $.get(base_url+"/finance/accounts/getL4Code/?acc_id="+val, function(data, status){

       $("#l4_code").html(data)
    });
}


(function($){
    $.fn.extend({
        donetyping: function(callback,timeout){
            timeout = timeout || 1e3; // 1 second default timeout
            var timeoutReference,
                doneTyping = function(el){
                    if (!timeoutReference) return;
                    timeoutReference = null;
                    callback.call(el);
                };
            return this.each(function(i,el){
                var $el = $(el);
                // Chrome Fix (Use keyup over keypress to detect backspace)
                // thank you @palerdot
                $el.is(':input') && $el.on('keyup keypress paste',function(e){
                    // This catches the backspace button in chrome, but also prevents
                    // the event from triggering too preemptively. Without this line,
                    // using tab/shift+tab will make the focused element fire the callback.
                    if (e.type=='keyup' && e.keyCode!=8) return;

                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference) clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function(){
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur',function(){
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });
})(jQuery);


$(document).ready(function() {

    $("#cheque_id").on("change", function() {
        var selectedChequeId = $(this).val();
        if(!selectedChequeId){
            $(".submitbtn").removeAttr("disabled")
            $("#cheq_num").html('');
        }
        $.get(base_url+"/finance/vendors/getChequeNum", { chequeId: selectedChequeId }, function(response) {
            if(response == 0){
                $("#cheq_num").html('All cheque numbers is occupied, create new cheque book.');
                $(".submitbtn").attr("disabled","disabled")
            }else{
                $("#chqnum").val(response)
            }
        })
            .fail(function(error) {
                console.error("Error: ", error);
            });
    });
});



// $('#chqnum').donetyping(function(){
//     if($("#cheque_id").val() == ''){
//         alert('Please Select Bank first..!')
//         $(".submitbtn").attr("disabled","disabled")
//         return;
//     }else{
//         $(".submitbtn").removeAttr("disabled")
//     }
//
//     $.get(base_url+"/finance/ledger/chequeNumExist/?cheque_number="+$("#chqnum").val()+"&cheque_id="+$("#cheque_id").val(),
//         function(data, status){
//
//         if(data == '0'){
//
//             $(".submitbtn").attr("disabled","disabled");
//             $("#cheq_num").html('Cheque number not available..');
//
//         }
//         else if(data == '2'){
//             $(".submitbtn").attr("disabled","disabled");
//             $("#cheq_num").html('already exist.');
//         }
//         else{
//             $(".submitbtn").removeAttr("disabled")
//             $("#cheq_num").html('')
//         }
//     });
//    // $('#example-output').text('Event last fired @ ' + (new Date().toUTCString()));
// });

$(".more").toggle(function(){
    $(this).text("less..").siblings(".complete").show();
}, function(){
    $(this).text("more..").siblings(".complete").hide();
});


function invoiceCal(){

    var tax = $("#tax_amount").val();
    var invoice = $("#invoice_amount").val();
    var total = parseInt(tax)+parseInt(invoice);
    console.log(total)
    if(!isNaN(total)){
        $("#total_invoice_amount").val(total)
    }else{
        $("#total_invoice_amount").val('')
    }
}

function getInvoices(vendor_id){
    if(vendor_id != ''){
        $.get(base_url+"/finance/vendors/getVendorInvoices/?vendor_id="+vendor_id,
            function(data, status){

                $("#invoices_id").html(data.options)

            });
    }else{
        $("#invoices_id").html('<option value="">Select</option>')
    }


}
function getInvoiceAmount(invoice_id){
    if(invoice_id != ''){
        $.getJSON(base_url+"/finance/vendors/getInvoiceAmount/"+invoice_id,
            function(data, status){

                $("#invoice_paid_amount").val(data.diff)
                $("#invoice_date").val(data.date)

            });
    }

}


$('#TxnDate').donetyping(function(){

    var invoice_id = $("#invoices_id").val();
    if( invoice_id != '') {
        var invoice_date = new Date($("#invoice_date").val());
        var TxnDate = new Date($("#TxnDate").val());
        console.log(invoice_date);
        console.log(TxnDate);
        if(TxnDate < invoice_date){
            $(".submitbtn").attr("disabled","disabled")
            $("#vms-error").html("Cannot post before invoice date")
        }else{
            $(".submitbtn").removeAttr("disabled","disabled")
            $("#vms-error").html("")
        }
    }
})

function openVendorForm(){
    $("#vendor_id").removeAttr('required');
    $("#vendor_id").prop( "disabled", true )
$("#vendorformbtn").html('....')
    $.get(base_url+"/finance/vendors/openVendorForm/",
        function(data, status){
            $("#vendor_form").html(data)

            $("#vendorformbtn").html('Add Vendor')
        });
}

function addMoreItems(){

    var rows = $(".rows_invoice").length;
    rows++;
    var accounts = $("#chartofaccount_1").html();

    $("#appendItems").append('<tr class="rows_invoice" id="rows_invoice_'+rows+'">' +
        '<td><select class="form-control" name="chartofaccount[]" id="chartofaccount_'+rows+'">'+accounts+'</select></td>' +
        '<td><input type="text" class="form-control" name="item_desc[]"></td>' +
        '<td><input type="number" onkeyup="calculateAmount()" class="form-control" id="price_'+rows+'" name="price[]"></td>' +
        '<td><input type="number" onkeyup="calculateAmount()" class="form-control" id="tax_'+rows+'" name="tax[]"></td>' +
        '  <td> <input name="merge[]" type="checkbox"></td>' +
        '<td><input type="number" readonly class="form-control total_rows_invoice" id="total_'+rows+'" name="total[]" ></td>' +
        '<td style="cursor:pointer" onclick="RemoveItems('+rows+')"><i style="font-size: 24px" class="icon-minus-circle2 mr-2"></i></td>' +
        '</tr>');
    $("#chartofaccount_"+rows).select2();

}

function RemoveItems(row){

    $("#rows_invoice_"+row).remove();
    calculateAmount()
}

function calculateAmount(rows){

   // var rows = $(".rows_invoice").length;

    var tax = $("#tax_"+rows).val();
    var price = $("#price_"+rows).val();
    var totalamount = parseInt(tax)+parseInt(price);

    if(!isNaN(totalamount)){
        $("#total_"+rows).val(totalamount)
        const sum = $(".total_rows_invoice").get().reduce(function(acc, inputField) {
            const value = parseFloat($(inputField).val()) || 0; // Parse value as a float, default to 0 if not a valid number
            return acc + value;
        }, 0);
       $("#invoice_amount").val(sum)
    }

}

function ApproveVendorInvoice(id){

    $.post(base_url+"/finance/vendors/approveVendorInvoice", {id:id ,_token:csrf_token}, function(data) {
      location.reload()
    }).fail(function() {
            alert("error");
    })
}


$(".select2").select2();
$("select").select2();
$("#city_id").select2();
 $("chartofaccount_1").select2();
$("#invoices_id").select2()
$("#vstatus").select2();
$("#txn_type").select2();
$("#l4_code").select2();
$("#main_acc").select2();
$("#reference").select2();
$("#vendor_id").select2();
$("#chartofaccount_"+1).select2();
$("#chartofaccount_"+2).select2();


