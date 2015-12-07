<?php
// Get all movies to display
//--------------------------
function GetAllMovies()
{

    // Set variable
    //-------------
    $aResult = [];
    $i = 0;
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

	return $aResult;

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

    return $aNbMoviesNotFound;
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
    $i = 0;
    $aResult[] = "";
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

    return $aResult;
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
    $i = 0;
    $j = 0;
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

    return $aResult;

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

	return $aResult;
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
    $aGenre = [];
    $i = 0;
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

    return $aGenre;
}