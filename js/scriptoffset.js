function showProcess ( sucsess, offset, action) {
    $('.progress').show();
    $('#runScript').hide();
    $('.bar').text('Загрузка');
    $('.bar').css('width', sucsess * 100 + '%');

    $('#runScript').click(function(){
        document.location.href=document.location.href
    });
    
    var city = $('#wp_cn_city_url').val();
    var region = $('#wp_cn_region_url').val();
    scriptOffset( offset, action, city, region);
}

function scriptOffset (offset, action, city_url, region_url) {
    $.ajax({
        url: site_url+'/wp-admin/admin-ajax.php',
        type: "POST",
        data_type: 'json',
        data: {
            "action": 'wp_cn_ajax_response',
            "act":action,
            'city_url': city_url,
            'region_url': region_url,
            "offset":offset
        },
        success: function(data){
            //console.log(data.arr);
            if(data.sucsess < 1) {
                showProcess(data.sucsess, data.offset, action);
            } else {
                $('.bar').css('width','100%');
                $('.bar').text('OK');
                $('#wp_cn_opt_names_added').attr('value','true');
                $('#wp_cn_settings_form').submit();
            }
        }
    });
}

$(document).ready(function() {

    $('#runScript').click(function() {
        $('.wp_cn_set_start_label').css({'display':'none'});
        $(this).css({'display':'none'});
        var action = $('#runScript').data('action');
        var city = $('#wp_cn_city_url').val();
        var region = $('#wp_cn_region_url').val();
    
        scriptOffset( 0, action, city, region);
        return false;
    });
        
    $('#refreshScript').click(function() {
        
        var action = $('#runScript').data('action');
        var city = $('#wp_cn_city_url').val();
        var region = $('#wp_cn_region_url').val();
    
        scriptOffset( 0, action, city, region);
        return false;
    });
    
});