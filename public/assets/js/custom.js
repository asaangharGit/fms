var base_url = $("meta[name=base_url]").attr("content");
var user_id = $("meta[name=user_id]").attr("content");
var csrf_token = $("meta[name=csrf-token]").attr("content");
var ip_address = $("meta[name=ip_address]").attr("content");
document.addEventListener('DOMContentLoaded', function() {
    $('.date-pick').datepicker();
});
var row=0;
$('.add-row').click(function(){
    if(row<50){
        row++;
        console.log(row);
        $(".gl_rows_"+row).show();
    }
});
$('.rem-row').click(function(){
    if(row>0){
        $(".gl_rows_"+row).hide();
        $(".gl_rows_"+row).find("input").val("");
        row--;
        console.log(row);
    }
});
$(".debit").keyup(function(){
    var granddebit = 0;
    $(".debit").each(function(){
        console.log($(this).val())
        granddebit+=parseInt($(this).val());
    });
    $(".granddebit").html(granddebit);
});
$(".credit").keyup(function(){
    var grandcredit = 0;
    $(".credit").each(function(){
        console.log($(this).val())
        grandcredit+=parseInt($(this).val());
    });
    $(".grandcredit").html(grandcredit);
});

$(document).ready(function(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition,showError);
    } else {
       alert("Geolocation is not supported by this browser.");
    }

})

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



$("#role_id").select2();

function checkPages(id){


    if (document.getElementById('main_'+id).checked) {
        $(".ck_"+id).prop('checked', true);

    } else {
        $(".ck_"+id).prop('checked', false);

    }

}

function checkinnerpages(id){
    $("#main_"+id).prop('checked', true);
}

function viewDataModal(url,id,modalName){

    jQuery('#showDetailModelTwoParamerter').modal('show', {backdrop: 'false'});
    jQuery('#showDetailModelTwoParamerter .modalTitle').html(modalName);
    jQuery('#showDetailModelTwoParamerter .modal-body').html('<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>');


    $.ajax({

        url: base_url+'/'+url+'',
        type: "GET",
        data: {id:id},
        success:function(data) {

            //setTimeout(function(){
            jQuery('#showDetailModelTwoParamerter .modal-body').html(data);
            //},1000);


        }
    });
}
