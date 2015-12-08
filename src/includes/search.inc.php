<?php
// Get all movies to display
//--------------------------
function GetAllMovies()
{

    // Set variable
    //-------------
    $aResult = [];
    $i = 0;
	$sPrint = "";
    ///////////////


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


	// Select all movies
    //------------------
    $sQuerie 	= " SELECT
					id_movie,
					title,
					poster_path
				FROM
					movies";
    $oResponse			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
    ////////////////////////////////////////////


	// Select genre of the movie
	//--------------------------
    while($aMovies = $oResponse->fetch())
	{

		// Set variable
		//-------------
		$aGenre = "";
		$aData 	= "";
		///////////////


		$sQuery2 = "
				SELECT
					genre
				FROM
					genres
				WHERE
					id_genre IN(SELECT id_genre FROM movie_genres WHERE id_movie = ".$aMovies['id_movie'].")";


		$oResponse2		= ExecuteQuerie($oMyDB, $sQuery2, 'SELECT');
    	////////////////////////////////////////////


    	// Add genre in tab
    	//-----------------
    	while($aGenres = $oResponse2->fetch())
		{
			$aGenre .= $aGenres['genre']." ";
		}
		////////////////////////////////////////////


		// Add all informations in tab
		//----------------------------
		$aData['title']			= $aMovies['title'];
        $aData['poster_path']	= $aMovies['poster_path'];
        $aData['id']			= $aMovies['id_movie'];
        $aData['genre']			= $aGenre;
        $aResult[$i] = $aData;

        $i ++;
        ////////////////////////////////////////////


	}

	// Print movies
	//-------------
    foreach ($aResult as $row)
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


	return $sPrint;

}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Get number of uncorrect movies to display to user
//--------------------------------------------------
function GetNumberUnCorrectMovie()
{

    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // Select number of movies not found
    //----------------------------------
    $sQuerie 	= " SELECT
					COUNT(id_correct) AS 'NbMoviesNotFound'
				FROM
					corrects";
    $oResponse			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
    $aNbMoviesNotFound 	= $oResponse->fetch();
    ////////////////////////////////////////////


    // Display message
	//----------------
    if($aNbMoviesNotFound['NbMoviesNotFound'] != 0)
    {
        $sDisplay = "You have ".$aNbMoviesNotFound['NbMoviesNotFound']." movie(s) that don't match";
    }
    else
    {
        $sDisplay = 'vide';
    }
	/////////////////////////////////////////////////////////////////

    return $sDisplay;
}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Get all uncorrect movies
//-------------------------
function GetUnCorrectMovie()
{

    // Set variable
    //-------------
    $i 			= 0;
    $aResult[] 	= "";
	$sDisplay 	= "<form id='formCorrect' method='post'";
	$j 			= 0;
    ////////////////


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // Select all uncorrect movies
    //----------------------------
    $sQuery 	= " SELECT
    				id_correct,
					filename_correct,
					filename_source
				FROM
					corrects";

    $oResponse	= ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
    ////////////////////////////////////////////


    // Insert information in table
    //----------------------------
    while($row = $oResponse->fetch())
    {
        $aData = [];

		$aData['id_correct']		= $row['id_correct'];
        $aData['filename_correct'] 	= $row['filename_correct'];
		$aData['filename_source']	= $row['filename_source'];
        $aResult[$i] = $aData;

        $i ++;
    }
    ////////////////////////////////////////////



	// Display all input type text with source filename
	//-------------------------------------------------
    foreach($aResult as $sMovies)
    {
        $sDisplay .="<div class='form-group'><input type='text' class='form-control' placeholder='".$sMovies['filename_source']."' value='".$sMovies['filename_source']."' name='nameCorrect".$j."'></div>";
        $sDisplay .="<div class='form-group'><input type='hidden' class='form-control' value='".$sMovies['filename_source']."' name='nameFile".$j."'></div>";
        $sDisplay .="<div class='form-group'><input type='hidden' class='form-control' value='".$sMovies['id_correct']."' name='id".$j."'></div>";
        $j++;
    }
	/////////////////////////////////////////////////////////////////


	// Display end of modal
	//---------------------
	$sDisplay .= '
		<div class="modal-footer">
			<div class="btn-group">
				<button class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
				<button id="correctmovie" class="btn btn-primary"><span class="glyphicon glyphicon-check"></span> Save</button>
			</div>
		</div>
	</form>';



    return $sDisplay;
}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Get description of one movie
//------------------------------
function GetFilmDescription($idMovie)
{

    // Set variable
    //-------------
    $i 			= 0;
    $j 			= 0;
	$sDisplay 	= "";
    ///////////////


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // Get all information in movie table
    //-----------------------------------
    $sQuerie = "SELECT
                    *
                FROM
                    movies
                WHERE
                    id_movie = ".$idMovie.";";


    $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
    //////////////////////////////////////////////////////////////


    // Get actors from the movie
    //--------------------------
    while($row = $oResponse->fetch())
    {
        $aData = [];
        $sQuerie = 'SELECT
                        name,
                        firstname
               FROM
                        actors
               INNER JOIN
                        movie_actors ON movie_actors.id_actor = actors.id_actor
               WHERE
                        movie_actors.id_movie = '.$row['id_movie'].'
               LIMIT 5';

        $oResponse2  = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
        //////////////////////////////////////////////////////////////


        // Put actors information in table
        //--------------------------------
        while($row2 = $oResponse2->fetch())
        {
            $aData2 = [];

            $aData2['name']            = $row2['name'];
            $aData2['firstname']       = $row2['firstname'];

            $aResult2[$j] = $aData2;

            $j ++;

        }
        //////////////////////////////////////////////////////////////


        // Insert all informations about the movies in table
        //--------------------------------------------------
        $aData['title']           = $row['original_title'];
        $aData['release']         = $row['release_date'];
        $aData['runtime']         = $row['runtime'];
        $aData['synopsis']        = $row['synopsis'];
        $aData['poster_path']     = "http://image.tmdb.org/t/p/w342".$row['poster_path'];
        $aData['file_path']       = $row['file_path'];
        $aData['actors']          = $aResult2;

        $aResult[$i] = $aData;
        $i ++;
        //////////////////////////////////////////////////////////////
    }



	// Display the description
	//------------------------
    $sDisplay ="
                <h1>".$aResult[0]['title']."</h1>
                <div class='col-md-8'>
		            <img src='".$aResult[0]['poster_path']."' />
		        </div>
		            <div class='col-md-3'>
              			<span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> ".date("d.m.Y", strtotime($aResult[0]['release']))." <br />
               			<span class='glyphicon glyphicon-time' aria-hidden='true'></span> ".$aResult[0]['runtime']." min <br />

               			<h2>Actors</h2>
    ";
	/////////////////////////////////////////////////////////////////


	// Display actors
	//---------------
   	foreach ($aResult[0]['actors'] as $aActor)
   	{
          $sDisplay .= $aActor['firstname']." ".$aActor['name']."<br/>";
   	}
	/////////////////////////////////////////////////////////////////


	// Display file path and synopsis
	//-------------------------------
    $sDisplay .= "
        			</div>
                    	<div class='col-md-12'>
                           <span class='glyphicon glyphicon-file' aria-hidden='true'></span> <span class='small'>".$aResult[0]['file_path']."</span>
                   			<h2>Synopsis</h2>
                   			".$aResult[0]['synopsis']."
            			</div>
            		<div class='clearfix'></div>";

    return $sDisplay;

}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Function call by search
//------------------------
function GetSomeMovies($aIdMovies)
{

	// Set variable
    //-------------
    $aResult 			= [];
    $i 					= 0;
	$sIds 				= "";
    $sFirstIteration 	= true;
	$sDisplay			= "";
    ///////////////


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


	// Check the value of $aIdMovies
	//------------------------------
    if(count($aIdMovies) != 0)
    {

		// Get all ids of movie find by the search
		//----------------------------------------
        foreach ($aIdMovies as $id)
        {
            if($sFirstIteration)
            {
                $sIds .= $id;
                $sFirstIteration = false;
            }
            else
            {
                $sIds .=", ".$id;
            }
        }
		//////////////////////////////////////////////////////////////


		// Select all movies
	    //------------------
	    $sQuerie 	= "
	    			SELECT
						id_movie,
						title,
						poster_path
					FROM
						movies
					WHERE
						id_movie IN (".$sIds.");";
	    $oResponse			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
	    ////////////////////////////////////////////


		// Select genre of the movie
		//--------------------------
	    while($aMovies = $oResponse->fetch())
		{

			// Set variable
			//-------------
			$aGenre = "";
			$aData 	= "";
			///////////////


			$sQuery2 = "
					SELECT
						genre
					FROM
						genres
					WHERE
						id_genre IN(SELECT id_genre FROM movie_genres WHERE id_movie = ".$aMovies['id_movie'].")";


			$oResponse2		= ExecuteQuerie($oMyDB, $sQuery2, 'SELECT');
	    	////////////////////////////////////////////


	    	// Add genre in tab
	    	//-----------------
	    	while($aGenres = $oResponse2->fetch())
			{
				$aGenre .= $aGenres['genre']." ";
			}
			////////////////////////////////////////////


			// Add all informations in tab
			//----------------------------
			$aData['title']			= $aMovies['title'];
	        $aData['poster_path']	= $aMovies['poster_path'];
	        $aData['id']			= $aMovies['id_movie'];
	        $aData['genre']			= $aGenre;
	        $aResult[$i] = $aData;

	        $i ++;
	        ////////////////////////////////////////////
		}


	}


	// Display movies find
	//--------------------
    foreach ($aResult as $row)
    {
        $sDisplay .= "
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

	return $sDisplay;
}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Function to search movies
//--------------------------
function GetSearchMovies($title, $actor, $genre, $year)
{

    // Set variable
    //-------------
    $aIdMovies = [];


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // Find by title
    //--------------
    if(isset($title) && $title != "" && (!isset($actor) || $actor == "") && (!isset($genre) || $genre == "") && (!isset($year) || $year == ""))
    {
        $sQueryM = "SELECT
                        id_movie
                    FROM
                        movies
                    WHERE
                        title LIKE ?;";

        $sTab = ['%'.$title.'%'];
        $oResponseM = ExecutePreparedQuerie($oMyDB, $sQueryM, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseM->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


    // Find by actors
    //---------------
    else if(isset($actor) && $actor != "" && (!isset($title) || $title == "") && (!isset($genre) || $genre == "") && (!isset($year) || $year == ""))
    {

        $sQueryA = "
        		SELECT
        			id_movie
        		FROM
        			movies
        		WHERE
        			id_movie IN (
								SELECT
									id_movie
								FROM
									movie_actors
								WHERE
									id_actor IN (
											SELECT
												id_actor
											FROM
												actors
											WHERE
												name LIKE ? OR firstname LIKE ?));";

        $sTab = [$actor.'%', $actor.'%'];
        $oResponseA = ExecutePreparedQuerie($oMyDB, $sQueryA, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseA->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////

    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


    // Find by genre
    //--------------
    else if(isset($genre) && $genre != "" && (!isset($title) || $title == "") && (!isset($actor) || $actor == "") && (!isset($year) || $year == ""))
    {

        $sQueryG = "SELECT
        				id_movie
        			FROM
        				movies
        			WHERE id_movie IN (
										SELECT
											id_movie
										FROM
											movie_genres
										WHERE
											id_genre IN (
														SELECT
															id_genre
														FROM
															genres
														WHERE genre LIKE ?));";
        $sTab = [$genre.'%'];
        $oResponseG = ExecutePreparedQuerie($oMyDB, $sQueryG, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////

    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


    // Find by year
    //-------------
    else if(isset($year) && $year != "" && (!isset($title) || $title == "") && (!isset($actor) || $actor == "") && (!isset($genre) || $genre == ""))
    {
        $sQueryY="SELECT
        			id_movie
        		FROM
        			movies
        		WHERE
        			release_date LIKE ?;";
        $sTab = [$year.'%'];
        $oResponseY = ExecutePreparedQuerie($oMyDB, $sQueryY, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////

    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by actors and title
	//---------------------------
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && (!isset($genre) || $genre == "") && (!isset($year) || $year == ""))
    {
        $sQueryTA = "SELECT id_movie FROM movies WHERE title LIKE ? AND id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?));";
        $sTab = ['%'.$title.'%', $actor.'%', $actor.'%'];
        $oResponseTA = ExecutePreparedQuerie($oMyDB, $sQueryTA, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseTA->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////

    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by actors and title
	//---------------------------
    else if(isset($title) && $title != "" && isset($genre) && $genre != "" && (!isset($actor) || $actor == "") && (!isset($year) || $year == ""))
    {
        $sQueryTG = "SELECT id_movie FROM movies WHERE title LIKE ? AND id_movie IN (
		SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))";
        $sTab = ['%'.$title.'%', $genre.'%'];
        $oResponseTG = ExecutePreparedQuerie($oMyDB, $sQueryTG, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseTG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by years and title
	//--------------------------
    else if(isset($title) && $title != "" && isset($year) && $year != "" && (!isset($actor) || $actor == "") && (!isset($genre) || $genre == ""))
    {
        $sQueryTY = "SELECT id_movie FROM movies WHERE title LIKE ? AND release_date LIKE ?;";
        $sTab = ['%'.$title.'%', $year.'%'];
        $oResponseTY = ExecutePreparedQuerie($oMyDB, $sQueryTY, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseTY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by actors and genre
	//---------------------------
    else if(isset($actor) && $actor != "" && isset($genre) && $genre != "" && (!isset($title) || $title == "") && (!isset($year) || $year == ""))
    {
        $sQueryAG = "SELECT id_movie FROM movies WHERE id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
		AND id_movie IN (
		SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?));";
        $sTab = [$actor.'%', $actor.'%', $genre.'%'];
        $oResponseAG = ExecutePreparedQuerie($oMyDB, $sQueryAG, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseAG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by actors and years
	//---------------------------
    else if(isset($actor) && $actor != "" && isset($year) && $year != "" && (!isset($title) || $title == "") && (!isset($genre) || $genre == ""))
    {
        $sQueryAY = "SELECT id_movie FROM movies WHERE id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN(SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
		AND release_date LIKE ?;";
        $sTab = [$actor.'%', $actor.'%', $year.'%'];
        $oResponseAY = ExecutePreparedQuerie($oMyDB, $sQueryAY, $sTab);
		//////////////////////////////////////////////////////////////


		// insert in array
		//----------------
        while($aDataM = $oResponseAY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by genre and years
	//--------------------------
    else if(isset($genre) && $genre != "" && isset($year) && $year != "" && (!isset($title) || $title == "") && (!isset($actor) || $actor == ""))
    {
        $sQueryGY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))
			AND release_date LIKE ?;";
        $sTab = [$genre.'%', $year.'%'];
        $oResponseGY = ExecutePreparedQuerie($oMyDB, $sQueryGY, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseGY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by title, actors and genre
	//----------------------------------
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && isset($genre) && $genre != "" && (!isset($year) || $year == ""))
    {
        $sQueryATG = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movies WHERE title LIKE ?)
			AND id_movie IN (SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND id_movie IN (SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?));";

        $sTab = ['%'.$title.'%', $actor.'%', $actor.'%', $genre.'%'];
        $oResponseATG = ExecutePreparedQuerie($oMyDB, $sQueryATG, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseATG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by title, actors and years
	//----------------------------------
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && isset($year) && $year != "" && (!isset($genre) || $genre == ""))
    {
        $sQueryATY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movies WHERE title LIKE ?)
			AND id_movie IN (SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND release_date LIKE ?;";

        $sTab = ['%'.$title.'%', $actor.'%', $actor.'%', $year.'%'];
        $oResponseATY = ExecutePreparedQuerie($oMyDB, $sQueryATY, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseATY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by actors, genre and years
	//----------------------------------
    else if(isset($actor) && $actor != "" && isset($genre) && $genre != "" && isset($year) && $year != "" && (!isset($title) || $title == ""))
    {
        $sQueryAGY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND id_movie IN (SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))
			AND release_date LIKE ?;";

        $sTab = [$actor.'%', $actor.'%', $genre.'%', $year.'%'];
        $oResponseAGY = ExecutePreparedQuerie($oMyDB, $sQueryAGY, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseAGY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }


	//-----------------------------------------------------------------------------------------------------------//
	//-----------------------------------------------------------------------------------------------------------//


	// Search by title, actors, genres and years
	//------------------------------------------
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && isset($genre) && $genre != "" && isset($year) && $year != "")
    {
        $sQueryTAGY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movies WHERE title LIKE ?)
			AND id_movie IN (SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND id_movie IN (SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))
			AND release_date LIKE ?;";

        $sTab = ['%'.$title.'%' ,$actor.'%', $actor.'%', $genre.'%', $year.'%'];
        $oResponseTAGY = ExecutePreparedQuerie($oMyDB, $sQueryTAGY, $sTab);
		//////////////////////////////////////////////////////////////


		// Insert in array
		//----------------
        while($aDataM = $oResponseTAGY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
		//////////////////////////////////////////////////////////////
    }

}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


function GetAllGenres()
{

    // Set variable
    //-------------
    $aGenre 	= [];
    $i 			= 0;
    $sDisplay 	= "<input class=\"form-control\" list=\"chose_genre\" type=\"search\" id=\"g\" name=\"g\"><datalist id=\"chose_genre\">";
    ///////////////


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // Set query
    //----------
    $sQuery = "
	SELECT
		genre
	FROM
		genres
	";

    $oResponse = ExecuteQuerie($oMyDB, $sQuery, "SELECT");
	///////////////////////////////////////////////////////


	// Insert result in array for return
	//----------------------------------
    while ($aDataG = $oResponse->fetch())
    {
        $aGenre[$i] = $aDataG['genre'];
        $i++;
    }
	///////////////////////////////////////////////////////


	// Display options
	//----------------
    foreach($aGenre AS $val)
    {
        $sDisplay.= "<option value=".$val."/>";
    }
    $sDisplay .= "</datalist>";

	/////////////////////////////////////////////////////////////////


    return $sDisplay;
}