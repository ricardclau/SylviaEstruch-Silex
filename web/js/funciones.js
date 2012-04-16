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

    $('#Enviar').click(function() {
      var e_nom = $('#nombre'), e_mail = $('#mail'), e_text = $('#texto');
      var regexpmail = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if(e_nom.val() == '') {
          alert(contacto.name_err); e_nom.focus(); return false;
      }
      if(e_mail.val() == '') {
          alert(contacto.mail_err1); e_mail.focus(); return false;
      }
      if(!regexpmail.test(e_mail.val())) {
          alert(contacto.mail_err2); e_mail.focus(); return false;
      }
      if(e_text.val() == '') {
          alert(contacto.text_err); e_text.focus(); return false;
      }

      $.post('/contacto/mail',$('#frm_contacto').serialize(),function(data) {          
          if($.trim(data) == 'OK') {
              alert(contacto.msg_ok);
          } else {
              alert(contacto.msg_err);
          }
      });
      return true;
    }).ajaxError(function() {       
       alert(contacto.msg_err);
    });
});

