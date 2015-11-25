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



    $oMyDB = mysqli_connect('localhost', 'root', '', 'my_movies');



    $sQuery = "
	SELECT
		id_movie,
		title,
		poster_path
	FROM
		movies
	";

    $oResponse = $oMyDB->query($sQuery) or die(mysqli_error());


    while ($row = mysqli_fetch_array($oResponse, MYSQLI_ASSOC))
    {
        $aGenre = "";

        $sQuery2 = "
		SELECT
			genre
		FROM
			genres
		WHERE
			id_genre IN(SELECT id_genre FROM movie_genres WHERE id_movie = ".$row['id_movie'].")
		";

        $oResponse2 = $oMyDB->query($sQuery2) or die(mysqli_error());

        while ($row2 = mysqli_fetch_array($oResponse2, MYSQLI_ASSOC))
        {
            $aGenre .= $row2['genre']." ";

        }


        $aData = [];

        $aData['title']			= $row['title'];
        $aData['poster_path']	= $row['poster_path'];
        $aData['id']			= $row['id_movie'];
        $aData['genre']			= $aGenre;
        $aResult[$i] = $aData;

        $i ++;


    }

    return $aResult;
}

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
					filename_correct
				FROM
					corrects";

    $oResponse	= ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
    ////////////////////////////////////////////


    // Insert information in table
    //----------------------------
    while($row = $oResponse->fetch())
    {
        $aData = [];

        $aData['filename_correct'] = $row['filename_correct'];
        $aResult[$i] = $aData;

        $i ++;
    }
    ////////////////////////////////////////////

    return $aResult;
}


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


// Function call by search
//------------------------
function GetSomeMovies($aIdMovies)
{

    // Set variable
    //-------------
    $aResult = [];
    $i = 0;
    ///////////////


    $oMyDB = mysqli_connect('localhost', 'root', '', 'my_movies');



    //Preparation des ID
    if(count($aIdMovies) != 0)
    {
        $sIds = "";
        $firstIteration = true;
        foreach ($aIdMovies as $id)
        {
            if($firstIteration)
            {
                $sIds .= $id;
                $firstIteration = false;
            }
            else
            {
                $sIds .=", ".$id;
            }
        }
        $sQuery = "
		SELECT
			id_movie,
			title,
			poster_path
		FROM
			movies
		WHERE
			id_movie IN (".$sIds.");
		";

        $oResponse = $oMyDB->query($sQuery) or die(mysqli_error());

        if($oResponse->num_rows != 0)
        {
            while ($row = mysqli_fetch_array($oResponse, MYSQLI_ASSOC))
            {
                $aGenre = "";

                $sQuery2 = "
				SELECT
					genre
				FROM
					genres
				WHERE
					id_genre IN(SELECT id_genre FROM movie_genres WHERE id_movie = ".$row['id_movie'].")
				";

                $oResponse2 = $oMyDB->query($sQuery2) or die(mysqli_error());

                while ($row2 = mysqli_fetch_array($oResponse2, MYSQLI_ASSOC))
                {
                    $aGenre .= $row2['genre']." ";

                }


                $aData = [];

                $aData['title']			= $row['title'];
                $aData['poster_path']	= $row['poster_path'];
                $aData['id']			= $row['id_movie'];
                $aData['genre']			= $aGenre;
                $aResult[$i] = $aData;

                $i ++;
            }
        }
    }

    return $aResult;
}


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
        $queryM = "SELECT
                        id_movie
                    FROM
                        movies
                    WHERE
                        title LIKE ?;";

        $tab = ['%'.$title.'%'];
        $responseM = ExecutePreparedQuerie($oMyDB, $queryM, $tab);
        while($aDataM = $responseM->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }


    // Find by actors
    else if(isset($actor) && $actor != "" && (!isset($title) || $title == "") && (!isset($genre) || $genre == "") && (!isset($year) || $year == ""))
    {
        $queryA = "SELECT id_movie FROM movies WHERE id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?));";
        $tab = [$actor.'%', $actor.'%'];
        $responseA = ExecutePreparedQuerie($oMyDB, $queryA, $tab);
        while($aDataM = $responseA->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }


    // Find by genre
    else if(isset($genre) && $genre != "" && (!isset($title) || $title == "") && (!isset($actor) || $actor == "") && (!isset($year) || $year == ""))
    {
        $queryG = "SELECT id_movie FROM movies WHERE id_movie IN (
		SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?));";
        $tab = [$genre.'%'];
        $responseG = ExecutePreparedQuerie($oMyDB, $queryG, $tab);
        while($aDataM = $responseG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }


    // Find by year
    //-------------
    else if(isset($year) && $year != "" && (!isset($title) || $title == "") && (!isset($actor) || $actor == "") && (!isset($genre) || $genre == ""))
    {
        $queryY="SELECT id_movie FROM movies WHERE release_date LIKE ?;";
        $tab = [$year.'%'];
        $responseY = ExecutePreparedQuerie($oMyDB, $queryY, $tab);
        while($aDataM = $responseY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par titre et acteur
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && (!isset($genre) || $genre == "") && (!isset($year) || $year == ""))
    {
        $queryTA = "SELECT id_movie FROM movies WHERE title LIKE ? AND id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?));";
        $tab = ['%'.$title.'%', $actor.'%', $actor.'%'];
        $responseTA = ExecutePreparedQuerie($oMyDB, $queryTA, $tab);
        while($aDataM = $responseTA->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par titre et genre
    else if(isset($title) && $title != "" && isset($genre) && $genre != "" && (!isset($actor) || $actor == "") && (!isset($year) || $year == ""))
    {
        $queryTG = "SELECT id_movie FROM movies WHERE title LIKE ? AND id_movie IN (
		SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))";
        $tab = ['%'.$title.'%', $genre.'%'];
        $responseTG = ExecutePreparedQuerie($oMyDB, $queryTG, $tab);
        while($aDataM = $responseTG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par titre et année
    else if(isset($title) && $title != "" && isset($year) && $year != "" && (!isset($actor) || $actor == "") && (!isset($genre) || $genre == ""))
    {
        $queryTY = "SELECT id_movie FROM movies WHERE title LIKE ? AND release_date LIKE ?;";
        $tab = ['%'.$title.'%', $year.'%'];
        $responseTY = ExecutePreparedQuerie($oMyDB, $queryTY, $tab);
        while($aDataM = $responseTY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par acteur et genre
    else if(isset($actor) && $actor != "" && isset($genre) && $genre != "" && (!isset($title) || $title == "") && (!isset($year) || $year == ""))
    {
        $queryAG = "SELECT id_movie FROM movies WHERE id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
		AND id_movie IN (
		SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?));";
        $tab = [$actor.'%', $actor.'%', $genre.'%'];
        $responseAG = ExecutePreparedQuerie($oMyDB, $queryAG, $tab);
        while($aDataM = $responseAG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par acteur et années
    else if(isset($actor) && $actor != "" && isset($year) && $year != "" && (!isset($title) || $title == "") && (!isset($genre) || $genre == ""))
    {
        $queryAY = "SELECT id_movie FROM movies WHERE id_movie IN (
		SELECT id_movie FROM movie_actors WHERE id_actor IN(SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
		AND release_date LIKE ?;";
        $tab = [$actor.'%', $actor.'%', $year.'%'];
        $responseAY = ExecutePreparedQuerie($oMyDB, $queryAY, $tab);
        while($aDataM = $responseAY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par genre et années
    else if(isset($genre) && $genre != "" && isset($year) && $year != "" && (!isset($title) || $title == "") && (!isset($actor) || $actor == ""))
    {
        $queryGY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))
			AND release_date LIKE ?;";
        $tab = [$genre.'%', $year.'%'];
        $responseGY = ExecutePreparedQuerie($oMyDB, $queryGY, $tab);
        while($aDataM = $responseGY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par titre, acteurs et genre
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && isset($genre) && $genre != "" && (!isset($year) || $year == ""))
    {
        $queryATG = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movies WHERE title LIKE ?)
			AND id_movie IN (SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND id_movie IN (SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?));";

        $tab = ['%'.$title.'%', $actor.'%', $actor.'%', $genre.'%'];
        $responseATG = ExecutePreparedQuerie($oMyDB, $queryATG, $tab);
        while($aDataM = $responseATG->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par titre, acteur et annee
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && isset($year) && $year != "" && (!isset($genre) || $genre == ""))
    {
        $queryATY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movies WHERE title LIKE ?)
			AND id_movie IN (SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND release_date LIKE ?;";

        $tab = ['%'.$title.'%', $actor.'%', $actor.'%', $year.'%'];
        $responseATY = ExecutePreparedQuerie($oMyDB, $queryATY, $tab);
        while($aDataM = $responseATY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par acteur, genre et années
    else if(isset($actor) && $actor != "" && isset($genre) && $genre != "" && isset($year) && $year != "" && (!isset($title) || $title == ""))
    {
        $queryAGY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND id_movie IN (SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))
			AND release_date LIKE ?;";

        $tab = [$actor.'%', $actor.'%', $genre.'%', $year.'%'];
        $responseAGY = ExecutePreparedQuerie($oMyDB, $queryAGY, $tab);
        while($aDataM = $responseAGY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }

//Recherche par titre, acteurs, genres, années
    else if(isset($title) && $title != "" && isset($actor) && $actor != "" && isset($genre) && $genre != "" && isset($year) && $year != "")
    {
        $queryTAGY = "SELECT id_movie FROM movies WHERE id_movie IN (
			SELECT id_movie FROM movies WHERE title LIKE ?)
			AND id_movie IN (SELECT id_movie FROM movie_actors WHERE id_actor IN (SELECT id_actor FROM actors WHERE name LIKE ? OR firstname LIKE ?))
			AND id_movie IN (SELECT id_movie FROM movie_genres WHERE id_genre IN (SELECT id_genre FROM genres WHERE genre LIKE ?))
			AND release_date LIKE ?;";

        $tab = ['%'.$title.'%' ,$actor.'%', $actor.'%', $genre.'%', $year.'%'];
        $responseTAGY = ExecutePreparedQuerie($oMyDB, $queryTAGY, $tab);
        while($aDataM = $responseTAGY->fetch())
        {
            $aIdMovies[] = $aDataM['id_movie'];
        }
        return GetSomeMovies($aIdMovies);
    }
}

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

    $sQuery = "
	SELECT
		genre
	FROM
		genres
	";

    $oResponse = ExecuteQuerie($oMyDB, $sQuery, "SELECT");

    while ($aDataG = $oResponse->fetch())
    {
        $aGenre[$i] = $aDataG['genre'];
        $i++;
    }

    return $aGenre;
}
?>