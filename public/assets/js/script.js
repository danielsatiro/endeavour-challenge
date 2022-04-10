var appBaseUrl = window.location.origin;
var path = '';
if (document.location.hostname === "hulk.meuprojetoweb.com.br") {
    path = '/admin-elo-editora/public';
} else if (document.location.host === "localhost:8888") {
    path = '/admin-elo-editora/public';
}

if ($('main').hasClass('login')) {
    $('footer').addClass('light');
    $('footer').addClass('login');
}

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});


// Disabled input

$('input').each(function(){
    var isDisabled = $(this).prop('disabled');

    if (isDisabled)
    {
        $(this).closest('.form-group').addClass('disabled');
    }
});


$(document).ready(function() {

    if($(window).width()>900){

        $(document).click(function(event) {
            if (!$(event.target).closest("nav.menu ul li, .header .wrap .right .user").length) {
                $('nav.menu ul.submenu').removeClass('active');
                $('nav.menu ul.submenu-lvl2').removeClass('active');
                $('.header .wrap .right ul.user-menu').hide('fast');
                $('.header .wrap .right .user').removeClass('active');
            }
        });
    }

    if($(window).width()<901){

        $(document).click(function(event) {
            if (!$(event.target).closest(".btn-menu, nav.menu ul.main-menu, .header .wrap .right .user").length) {
                $('nav.menu ul.main-menu').removeClass('active');
                $('.btn-menu').removeClass('active');
                $('.menu-trigger').removeClass('active');
                $('nav.menu ul.submenu').removeClass('active');
                $('nav.menu ul.submenu-lvl2').removeClass('active');
                $('.header .wrap .right ul.user-menu').hide('fast');
                $('.header .wrap .right .user').removeClass('active');
            }
        });
    }
    

    // Menu Mobile

    $('.btn-menu').click(function(){
        $(this).toggleClass('active');
        $('nav.menu ul.main-menu').toggleClass('active');
        $('.menu-trigger').toggleClass('active');
        $('nav.menu ul.submenu').removeClass('active');
        $('nav.menu ul.submenu-lvl2').removeClass('active');
    });

    $('nav.menu ul li a').click(function(){

        $_this = $(this).closest('li.has-children').find('ul.submenu');
        $('nav.menu ul.submenu').not($_this).removeClass('active');
    });


    if($(window).width()>900){

        $('ul.main-menu li.has-children').click(function(){
            $(this).find('ul.submenu').toggleClass('active');
        });

        $('ul.submenu li.has-children').click(function(){
            $(this).find('ul.submenu-lvl2').toggleClass('active');
        });
    }

    if($(window).width()<901){


        $('ul.main-menu li.has-children').click(function(){
            $(this).find('ul.submenu').toggleClass('active');
        });

        $('ul.submenu li.has-children').click(function(){
            $(this).find('ul.submenu-lvl2').toggleClass('active');
        });
    }

    $('.header .wrap .right .user').click(function(){
        $(this).toggleClass('active');
        $('.header .wrap .right ul.user-menu').slideToggle('fast');
    });

    $('.exit-admin').click(function(){

        jQuery('#modalExit').modal({
            keyboard: false
        });
        jQuery('#modalExit').modal('show');

    });

    $('#modalExit .exit').click(function(e){
        e.preventDefault();
        window.location.href = appBaseUrl + path
    });


    $('.show-pass').click(function () {
        $(this).siblings('.input-pass').toggleClass('visible-pass');
        $(this).toggleClass('slash');

        if ($('.input-pass').hasClass('visible-pass')) {
            $(this).siblings('.input-pass').attr('type', 'text');
        } else {
            $(this).siblings('.input-pass').attr('type', 'password');
        }

    });


    // Masks

    // Phone Mask

    // var SPMaskBehavior = function (val) {
    //     return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    //     },
    //     spOptions = {
    //     onKeyPress: function(val, e, field, options) {
    //     field.mask(SPMaskBehavior.apply({}, arguments), options);
    //     }
    // };

    // $('.tel').mask(SPMaskBehavior, spOptions);


    // Checkbox

    $('.checkbox label').on('change', function() {

        if($(this).find('input').is(':checked')){
            $(this).addClass('checked');
        } else {
            $(this).removeClass('checked');
        }
        
    });


    // Form Submit

    $('#formLogin').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {


        }
    });

    $('#formResetUser').validator().on('submit', function (e) {
        if (e.isDefaultPrevented()) {
          

        } else {
            e.preventDefault();
            
            var formData = new FormData(this);
            var action = $(this).attr('action');            
            
            $.ajax({
                url: action,
                type: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    jQuery('#modalAlert').modal({
                        keyboard: false
                    });
                    jQuery('#modalAlert').modal('show');
                    jQuery('#modalAlert').find('.modal-title').text('Obrigado!');
                    jQuery('#modalAlert').find('.modal-body p').text('Sua senha foi alterada com sucesso.');
                    jQuery('#modalAlert').find('.modal-footer .btn-custom').text('Fechar');

                    $("input").val("");
                },
                error: function(response) {
                    var messages = '';
                    $.each(response.responseJSON.errors, function (key, value) {
                        $.each(value, function (k, message) {
                            messages += message + '\n';
                        });
                    });
                    alert(messages);
                },
                fail: function(response) {
                    alert(response.message);
                }
            });

        }
    });
    

    $(".change-status").click(function() {
        var token = $("meta[name='csrf-token']").attr("content");

        $.ajax({
            url: $(this).attr('href'),
            type: 'PUT',
            data: {"_token": token},
            success: function(response) {
                document.location.reload(true);
            },
            fail: function(response) {
                alert(response.message);
            }
        });
        return false;
    });

    $('.counter').each(function() {
        var $this = $(this),
            countTo = $this.attr('data-count');

        $({ countNum: $this.text()}).animate({
          countNum: countTo
        },

        {

          duration: 500,
          easing:'linear',
          step: function() {
            $this.text(Math.floor(this.countNum));
          },
          complete: function() {
            $this.text(this.countNum);
            //alert('finished');
          }

        });



    });

    jQuery('.inputFile').each(function(){

        var $_this = $(this);

         $_this.change(function() {
            var i = jQuery(this).prev('label').clone();
            var file =  $_this[0].files[0].name;
            jQuery(this).next('#inputFileName').val(file);
        });
    });

    $('.form-group').on('change', '#state_id', function () {
        var stateId = $(this).val();

        if (stateId === '') {
            return false;
        }

        var options = '<option value="">Selecione</option>';
        $.ajax({
            type:'GET',
            url: appBaseUrl + path + '/api/states/' + stateId + '/cities',
            success:function(data){                
                $.each(data, function (key, value) {
                    options += '<option value="' + value.name + '">' + value.name + '</option>';
                });
                $('#city').empty().append(options);
                $('#divCity').show();
            }
        });
    });

    // Custom Select

    $('.select-custom').selectric();

});

function deleteConfirmation(elementId, itemName)
{
    event.preventDefault();
    
    var text = 'Confirma a exclusão desse item?';
    if (itemName !== undefined) {
        text = 'Confirma a exclusão do(a) ' + itemName + '?';
    }
    if (confirm(text)) {
        document.getElementById(elementId).submit();
    }
}


