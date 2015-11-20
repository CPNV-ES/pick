$(document).ready(function() {
    // ONCLICK REVEALS FILTER MENU
    $('#btn').click(function() {
        $('#filter_menu').slideToggle("fast");
    });

    $('#y_link').click(function() {


        $('#y').slideToggle("fast");
    });

    $('#g_link').click(function() {


        $('#g').slideToggle("fast");
    });

    $('#a_link').click(function() {


        $('#a').slideToggle("fast");
    });
    /*IMG AFTER SNEAK MENU*/
    $('.tr').prepend('<img class="trimg" src="assets/img/tr.png">');

    /*ROTATE IMG AFTER SNEAK MENU */


    $(".trimg").rotate({
        bind:
        {
            click: function(){
                $(this).rotate({ angle:0,animateTo:78,easing: $.easing.easeInOutExpo })
            }
        }

    });

});