function setCookie (url, offset){
    var ws=new Date();
    if (!offset && !url) {
            ws.setMinutes(10-ws.getMinutes());
        } else {
            ws.setMinutes(10+ws.getMinutes());
        }
    document.cookie="scriptOffsetUrl="+url+";expires="+ws.toGMTString();
    document.cookie="scriptOffsetOffset="+offset+";expires="+ws.toGMTString();
}

function getCookie(name) {
    var cookie = " " + document.cookie;
    var search = " " + name + "=";
    var setStr = null;
    var offset = 0;
    var end = 0;
    if (cookie.length > 0) {
        offset = cookie.indexOf(search);
        if (offset != -1) {
            offset += search.length;
            end = cookie.indexOf(";", offset)
            if (end == -1) {
                end = cookie.length;
            }
            setStr = unescape(cookie.substring(offset, end));
        }
    }
    return(setStr);
}

function showProcess (url, sucsess, offset, action) {
    $('#url, #refreshScript').hide();
    $('.progress').show();
    $('#runScript').text('Стоп!');
    $('.bar').text(url);
    $('.bar').css('width', sucsess * 100 + '%');
    setCookie(url, offset);

    $('#runScript').click(function(){
            document.location.href=document.location.href
        });
    
    scriptOffset(url, offset, action);
}

function scriptOffset (url, offset, action) {
    $.ajax({
        url: site_url+'/wp-admin/admin-ajax.php',
        type: "POST",
        data: {
            "action": 'wp_cn_ajax_response',
            "act":action,
            "url":url,
            "offset":offset
        },
        success: function(data){
            console.log(data);
            //data = $.parseJSON(data);
            if(data.sucsess < 1) {
                showProcess(url, data.sucsess, data.offset, action);
                } else {
                setCookie();
                $('.bar').css('width','100%');
                $('.bar').text('OK');
                $('#runScript').text('Еще');
                }
        }
    });
}

$(document).ready(function() {

var url = getCookie("scriptOffsetUrl");
var offset = getCookie("scriptOffsetOffset");

if (url && url != 'undefined') {		
        $('#refreshScript').show();
        $('#runScript').text('Продолжить');
        $('#url').val(url);
        $('#offset').val(offset);
    }

$('#runScript').click(function() {
    
        var action = $('#runScript').data('action');
        var offset = $('#offset').val();
        var url = $('#url').val();
        
        if ($('#url').val() != getCookie("scriptOffsetUrl")) {
                setCookie();
                scriptOffset(url, 0, action);
            } else {
                scriptOffset(url, offset, action);
            }
        return false;
    });
    
$('#refreshScript').click(function() {
    
        var action = $('#runScript').data('action');
        var url = $('#url').val();
    
        setCookie();
        scriptOffset(url, 0, action);
        return false;
    });
    
});