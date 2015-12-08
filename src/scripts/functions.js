$(document).ready(function()
{

    // Display all movies in DB
    //-------------------------
    $('#list').load("./src/includes/call_functions.php?todo=getallmovies");
    $('#genre').load("./src/includes/call_functions.php?todo=getallgenres");

    $('#notfound').load("./src/includes/call_functions.php?todo=moviesnotfound", function(e)
    {
        if (e != 'vide')
        {
            $('#notfound').text(e);
            $('#correct_string').show();
        }
    });


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


    // Get click and launch scan
    //--------------------------
    $(document).on('click', '#lScan', function()
    {

        // Display modal for scan in progress
        //-----------------------------------
        $('#overlay').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $(".overlay-message").show();



        // Call function to scan user's disk
        //----------------------------------
        $('#list').load("./src/includes/call_functions.php?todo=scan", function()
        {

            // Take off modal
            //---------------
            $('#overlay').modal('hide');
            $(".overlay-message").hide();
            $('#correct_string').show();


            // Display number of movies not found if there is not found movies
            //-----------------------------------
            $('#notfound').load("./src/includes/call_functions.php?todo=moviesnotfound", function(e)
            {
                if (e != '')
                {
                    $('#notfound').text(e);
                    $('#correct_string').show();
                }
            });

        });
    });


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


    // Get click and lauch correction of the not found movies
    //-------------------------------------------------------
    $(document).on('click', '#correctmovie', function ()
    {

        // Hide modal form
        //----------------
        $('#modalplus').modal('hide');


        // Display modal for scan in progress
        //-----------------------------------
        $('#overlay').modal(
        {
            show: true,
            backdrop: 'static',
            keyboard: false
        });
        $(".overlay-message").show();


        // Call function to correct movies
        //--------------------------------
        $.post( "./src/includes/call_functions.php?todo=searchCorrectedMovie", $("#formCorrect").serialize(), function(e)
        {


            // Call function to display all movies
            //----------------------------------
            $('#list').load("./src/includes/call_functions.php?todo=getallmovies", function()
            {

                // Take off modal
                //---------------
                $('#overlay').modal('hide');
                $(".overlay-message").hide();
                $('#correct_string').show();


                // Display number of movies not found if there is not found movies
                //----------------------------------------------------------------
                $('#notfound').load("./src/includes/call_functions.php?todo=moviesnotfound", function(e)
                {
                    if (e != '')
                    {
                        $('#notfound').text(e);
                        $('#correct_string').show();
                    }
                });
            });
        });
        return false;
    });


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//

    // Modal for correct wrong movies
    //-------------------------------
    $(document).on('click', '#modal', function()
    {

        $('#modalcontent').load("./src/includes/call_functions.php?todo=displaymodalcorrect", function()
        {

            $('#modalplus').modal(
            {
                show: true,
                keyboard: true
            });
        });

        return false
    });


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


    // Get detail from movies
    //-----------------------
    $(document).on('click', '#detailfilm', function(e)
    {

        $('#modalcontent').load("./src/includes/call_functions.php?todo=showDetails&id="+$(this).val(), function()
        {
            $('#modalplus').modal(
            {
                show: true,
                keyboard: true
            });
        });
    });


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//

    // Search by user
    //---------------
    fieldTitle  = "";
    fieldAuthor = "";
    fieldGenre  = "";
    fieldYear   = "";
    /////////////////


    // Search by title
    //----------------
    $(document).on('input', '#t', function ()
    {
        fieldTitle = $("#t").val();
        $("#list").load("src/includes/call_functions.php?todo=searchMovies&queryT="+encodeURI(fieldTitle)+"&queryA="+encodeURI(fieldAuthor)+"&queryG="+encodeURI(fieldGenre)+"&queryY="+encodeURI(fieldYear));

    });
    ///////////////////////////////////////////////////


    // Search by actors
    //-----------------
    $(document).on('input', '#a', function ()
    {
        fieldAuthor = $("#a").val();
        $("#list").load("src/includes/call_functions.php?todo=searchMovies&queryT="+encodeURI(fieldTitle)+"&queryA="+encodeURI(fieldAuthor)+"&queryG="+encodeURI(fieldGenre)+"&queryY="+encodeURI(fieldYear));

    });
    ///////////////////////////////////////////////////


    // Search by genre
    //----------------
    $(document).on('input', '#g', function ()
    {
        fieldGenre = $("#g").val();
        $("#list").load("src/includes/call_functions.php?todo=searchMovies&queryT="+encodeURI(fieldTitle)+"&queryA="+encodeURI(fieldAuthor)+"&queryG="+encodeURI(fieldGenre)+"&queryY="+encodeURI(fieldYear));

    });
    ///////////////////////////////////////////////////


    // Search by years
    //----------------
    $(document).on('input', '#y', function ()
    {
        fieldYear =$("#y").val();
        $("#list").load("src/includes/call_functions.php?todo=searchMovies&queryT="+encodeURI(fieldTitle)+"&queryA="+encodeURI(fieldAuthor)+"&queryG="+encodeURI(fieldGenre)+"&queryY="+encodeURI(fieldYear));

    });
    ///////////////////////////////////////////////////



});