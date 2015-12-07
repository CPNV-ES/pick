<?php

// Include config and functions files
//-----------------------------------
include("../config/config.php");
foreach(glob('./*.inc.php') as $fileName)
{
    include_once $fileName;
}
////////////////////////////////////////




// Switch to call php function by jquery load
//-------------------------------------------
switch($_GET['todo'])
{

	// Scan user directory, add movies don't found and display movies found
	//---------------------------------------------------------------------
    case 'scan':


		// Call function
		//--------------
		$aMovieFromDisk = dirToArray(USERFOLDER);
	    $aMovieNotFound = SearchInMovieDB($aMovieFromDisk);

        AddUnCorrectMovie($aMovieNotFound);
		/////////////////////////////////////////////////////////////////


        // Call function to get film and print it
        //---------------------------------------
        $aMovie	= GetAllMovies();

        $sPrint = "";



		// Print movies
		//-------------
        foreach ($aMovie as $row)
        {

            $sPrint .= "
			<div class='col-xs-3 col-md-3 col-sm-3 col-centered pado'>
                <div class='cuadro_intro_hover' style='background-color:#cccccc;'>
                    <p style='text-align:center;'>
                        <img src='https://image.tmdb.org/t/p/w185".$row['poster_path']."' class='img-responsive' alt=''>

                    </p>
                    <div class='caption'>
                        <div class='blur'></div>
                        <div class='caption-text'>
                            <h3>".$row['title']."</h3>
                            <p>".$row['genre']."</p>
                            <button id='detailfilm' value='".$row['id']."'>+</button>
                        </div>
                    </div>
                </div>
             </div>

			";
        }
		/////////////////////////////////////////////////////////////////

        print($sPrint);

        break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Display to user how many movies not found
	//--------------------------------------------
    case 'moviesnotfound':


		// Call function
		//--------------
        $aNbMoviesNotFound = GetNumberUnCorrectMovie();
		/////////////////////////////////////////////////////////////////


		// Display message
		//----------------
        if($aNbMoviesNotFound['NbMoviesNotFound'] != 0)
        {
            print("You have ".$aNbMoviesNotFound['NbMoviesNotFound']." movie(s) that don't match");
        }
        else
        {
            print('vide');
        }
		/////////////////////////////////////////////////////////////////

        break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Display modal to correct movies not found
	//------------------------------------------
    case 'displaymodalcorrect':


		// Set variable
		//-------------
		$sPrint = "<form id='formCorrect' method='post'";
		$i		= 0;
		/////////////////////////////////////////////////////////////////


		// Call function
		//--------------
        $aUnCorrectMovies = GetUnCorrectMovie();


		// Display all input type text with source filename
		//-------------------------------------------------
        foreach($aUnCorrectMovies as $sMovies)
        {
            $sPrint .="<div class='form-group'><input type='text' class='form-control' placeholder='".$sMovies['filename_source']."' value='".$sMovies['filename_source']."' name='nameCorrect".$i."'></div>";
            $sPrint .="<div class='form-group'><input type='hidden' class='form-control' value='".$sMovies['filename_source']."' name='nameFile".$i."'></div>";
            $sPrint .="<div class='form-group'><input type='hidden' class='form-control' value='".$sMovies['id_correct']."' name='id".$i."'></div>";
            $i++;
        }
		/////////////////////////////////////////////////////////////////


		// Display end of modal
		//---------------------
		$sPrint .= '
			<div class="modal-footer">
				<div class="btn-group">
					<button class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
					<button id="correctmovie" class="btn btn-primary"><span class="glyphicon glyphicon-check"></span> Save</button>
				</div>
			</div>
		</form>';


        print($sPrint);
		/////////////////////////////////////////////////////////////////

        break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Display in modal the description of the movie
	//----------------------------------------------
    case 'showDetails' :


		// Call function with the id of the movie
		//---------------------------------------
        $aData = GetFilmDescription($_GET['id']);


		// Display the description
		//------------------------
        $sPrint ="
                    <h1>".$aData[0]['title']."</h1>
                    <div class='col-md-8'>
			            <img src='".$aData[0]['poster_path']."' />
			        </div>
			            <div class='col-md-3'>
                  			<span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> ".date("d.m.Y", strtotime($aData[0]['release']))." <br />
                   			<span class='glyphicon glyphicon-time' aria-hidden='true'></span> ".$aData[0]['runtime']." min <br />

                   			<h2>Actors</h2>
        ";
		/////////////////////////////////////////////////////////////////


		// Display actors
		//---------------
       	foreach ($aData[0]['actors'] as $aActor)
       	{
              $sPrint .= $aActor['firstname']." ".$aActor['name']."<br/>";
       	}
		/////////////////////////////////////////////////////////////////


		// Display file path and synopsis
		//-------------------------------
        $sPrint .= "
	        			</div>
	                    	<div class='col-md-12'>
	                           <span class='glyphicon glyphicon-file' aria-hidden='true'></span> <span class='small'>".$aData[0]['file_path']."</span>
	                   			<h2>Synopsis</h2>
	                   			".$aData[0]['synopsis']."
	            			</div>
	            		<div class='clearfix'></div>";

        print($sPrint);
		/////////////////////////////////////////////////////////////////

        break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Display all movies when user comme to the website
	//--------------------------------------------------
    case 'getallmovies':


		// Set variable
		//-------------
		$sPrint = "";


        // Call function to get film and print it
        //---------------------------------------
        $aMovie = GetAllMovies();


		// Display all movies
		//-------------------
        foreach ($aMovie as $row)
        {

            $sPrint .= "
			<div class='col-xs-3 col-md-3 col-sm-3 col-centered pado'>
                <div class='cuadro_intro_hover' style='background-color:#cccccc;'>
                    <p style='text-align:center;'>
                        <img src='https://image.tmdb.org/t/p/w185".$row['poster_path']."' class='img-responsive' alt=''>

                    </p>
                    <div class='caption'>
                        <div class='blur'></div>
                        <div class='caption-text'>
                            <h3>".$row['title']."</h3>
                            <p>".$row['genre']."</p>
                            <button id='detailfilm' value='".$row['id']."'>+</button>
                        </div>
                    </div>
                </div>
             </div>
			";
		}

		print($sPrint);
		/////////////////////////////////////////////////////////////////

		break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Search function and display result
	//-----------------------------------
    case 'searchMovies':

    	// Set variable
    	//-------------
		$sPrint = "";


    	// Call function
    	//--------------
        $aMovie = GetSearchMovies($_GET['queryT'], $_GET['queryA'], $_GET['queryG'], $_GET['queryY']);


		// Check if somethink is write in each search fields
		//---------------------------------------------------
        if(count($aMovie) == 0 && ($_GET['queryT'] != "" || $_GET['queryA'] != "" || $_GET['queryG'] != "" || $_GET['queryY'] != ""))
        {
        	// KO
            $sPrint = "<div id='notfoundcontent'>NO MOVIE FOUND !</div>";

        }
        else if(count($aMovie) == 0 && $_GET['queryT'] == "" && $_GET['queryA'] == "" && $_GET['queryG'] == "" && $_GET['queryY'] == "")
        {
        	// OK
            $aMovie = GetAllMovies();
        }


		// Display movies find
		//--------------------
        foreach ($aMovie as $row)
            {
                $sPrint .= "
                <div class='col-lg-3 col-centered pado'>
                    <div class='cuadro_intro_hover' style='background-color:#cccccc;'>
                        <p style='text-align:center;'>
                            <img src='https://image.tmdb.org/t/p/w185".$row['poster_path']."' class='img-responsive' alt=''>

                        </p>
                        <div class='caption'>
                            <div class='blur'></div>
                            <div class='caption-text'>
                                <h3>".$row['title']."</h3>
                                <p>".$row['genre']."</p>
                                <button id='detailfilm' value='".$row['id']."'>+</button>
                            </div>
                        </div>
                    </div>
                 </div>
            	";
            }

            print($sPrint);
			/////////////////////////////////////////////////////////////////


        break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Execute a new search about uncorrect movies with change of user
	//----------------------------------------------------------------
	case 'searchCorrectedMovie':

		SearchCorrectedMoviesInMovieDB();

	break;


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


	// Get all genre for search field
	//-------------------------------
    case 'getallgenres':


    	// Call function
    	//--------------
        $aGenre = GetAllGenres();


		// Set input type select
		//----------------------
        $sPrint = "<input class=\"form-control\" list=\"chose_genre\" type=\"search\" id=\"g\" name=\"g\"><datalist id=\"chose_genre\">";


		// Display options
		//----------------
        foreach($aGenre AS $val)
        {
            $sPrint.= "<option value=".$val."/>";
        }
        $sPrint .= "</datalist>";


        print($sPrint);
		/////////////////////////////////////////////////////////////////


        break;

}

?>