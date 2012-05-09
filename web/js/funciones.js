$(document).ready(function() {
    $('#obras img').animate({opacity: 0.5},"fast");
    $('#obras li span.contenedor').hover(
       function(){
            $(this).children('a').filter(':first').children('img').stop().animate({opacity: 1.0},"fast");
            $(this).children('span.text_hidden').stop().animate({top:'110px'},"fast");
        },
        function(){
            $(this).children('a').filter(':first').children('img').stop().animate({opacity: 0.5},"fast");
            $(this).children('span.text_hidden').stop().animate({top:'140px'},"fast");

        });
    
    var antiguaclass = '';
    $("#obras a").each(function() {
       if(this.className != antiguaclass) {
           antiguaclass = this.className;
           $('#obras a.'+this.className).lightBox({
            imageBlank: '/images/lightbox-blank.gif',
            imageLoading: '/images/lightbox-ico-loading.gif',
            imageBtnClose: '/images/lightbox-btn-close.gif',
            imageBtnPrev: '/images/lightbox-btn-prev.gif',
            imageBtnNext: '/images/lightbox-btn-next.gif',
            overlayBgColor: '#FFFFFF',
            overlayOpacity: 0.7            
           });
       }
    });

    $('#frm_contacto').submit(function() {
        $.ajax({
            type: 'POST',
            url:  $(this).attr('action'),
            data: $(this).serialize(),
            success: function(data, textStatus, jqXHR) {
                alert(data.msg);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                var msg = 'Error:\n';
                var jsonResponse = jQuery.parseJSON(jqXHR.responseText);
                $.each(jsonResponse.errors, function(index, value) {
                    msg += $('label[for='+ index.replace('[', '').replace(']', '') + ']').html() + ': ' + value + '\n';
                });
                alert(msg);
            }
        });

        return false;
    });
});

