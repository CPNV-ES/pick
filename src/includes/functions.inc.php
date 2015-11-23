<?php
require("../config/config.php");

set_time_limit(240);

function dirToArray($sDir)
{

    // Variable
    //---------
    $aResult	= array();
    $aReturn	= array();
    $aDir		= scandir($sDir);
    ////////////////////////////////////////////


    // Define character to replace in a array
    //---------------------------------------
    $aReplace2 = array (
        "zone-telechargement.com",
        "-",
        "^",
        "_",
        ".",
        ",",
        "txt",
        "{",
        "}",
        "[",
        "]",
        "FRENCH",
        " ld ",
        "rip",
        " mind ",
        "fwd",
        "dvd",
        "ac3",
        "dvdrip",
        "divx",
        "www",
        "avi",
        "xvid",
        "mp4",
        "telechargement",
        "dvb",
        "azerty",
        "hdtv",
        "jmt",
        "epz",
        "vostfr",
        "liberteam",
        "subforced",
        "true",
        " ",
        "hd",
        "limited",
        "720p",
        "bluray",
        "x264",
        "cpasbien.io",
        "mkv",
        "carpediem",
        "utt",
        "autopsie",
        "cpasbien.pw",
        "h264",
        "mhd",
        " fr ",
        " bd ",
        " br ",
        "qcp",
        "cpasbien.pe",
        "cpasbien.me",
        "aymo",
        "unrated",
        "torrent411",
        "repack",
        "thx",
        "dvdrip",
        "truefrench",
        "downparadise",
        "subforced",
        "xvid",
        "xvidfr"
    );


    $aReplace = array (
    			".",
    			"_",
    			"-",
    			" ",

	);
    ////////////////////////////////////////////



    // Browse the array of path
    //-------------------------
    foreach ($aDir as $key => $sValue)
    {
        if (!in_array($sValue,array(".","..")))
        {

            // Check if it's a directory
            //--------------------------
            if(is_dir($sDir . DIRECTORY_SEPARATOR . $sValue))
            {

                // Doesn't go in directory that contains (saison or season)
                //--------------------------------------------------------
                if(!preg_match_all("#(saison|season)#i", $sValue))
                {
                    $aResult[$sValue] = dirToArray($sDir . DIRECTORY_SEPARATOR . $sValue);
                }

            }
            else
            {
                // Replace / in \ for Window path
                //-------------------------------
                $sOriginalPath = $sDir;
                $sPath = str_replace("/", "\\", $sDir . DIRECTORY_SEPARATOR . $sValue);
                $sPath = str_replace("\\\\", "\\", $sPath);
				/////////////////////////////////

                preg_match('/^(\[.*\])?[_\.\s]?(([a-zA-Z0-9éèàë]{1,})([_\.\s\-]([A-Z]?([a-zéèàë&]{1,}|[IMVX]{1,})?|(?!(19|20|21)[0-9]{2})[0-9]{1,}|\%\![0-9]{1,}))*)([_\.\s]((\(?([0-9]){4}\)?|[A-Z]{4,}|(([sSeE](aison|eason|pisode)?)[_\s]?[0-9]{1,})[\s-]*(([sSeE](aison|eason|pisode)?)[_\s]?[0-9]{1,})?).*))?\.(avi|mp4|mov|mpg|mpa|wma)$/', utf8_encode($sValue), $matches);


				// Si preg_match ne trouve rien
				//-----------------------------
				if(!isset($matches[2]))
				{

					// REFAIRE REGEXP OU AUTRE MANIERE
					$sValueParse = str_ireplace($aReplace2, " ", $sValue);
				}
				else
				{

					// RESULTAT DE JOHN
					$sValueParse = $matches[2];
				}


                // Encode for correct visibility
                //------------------------------
                $sFichierTemp   = str_ireplace($aReplace, "+", utf8_encode($sValueParse));


                // Insert films with source name
                //------------------------------
                $aReturn[] .= $sFichierTemp.";".$sValue.";".$sOriginalPath;

            }
        }
    }
    return $aReturn;

}



function SearchAndInsert($aFile)
{





    // Set variable
    //-------------
    $aFileNotFound = "";


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    foreach($aFile as $sFichier)
    {

        // Split correct file name and source file name
        //---------------------------------------------
        $aFichier = explode(";", $sFichier);


        // Try to find the film in themoviedb's DB
        //----------------------------------------
        $aDataText = file_get_contents("http://api.themoviedb.org/3/search/movie?api_key=".THEDBMOVIEKEY."&query=".$aFichier[0]);


        // Return JSON. Must decode
        //-------------------------
        $aDataJsonMovie = json_decode($aDataText);


        // Check if the name of the file was found in themoviedb
        //------------------------------------------------------
        if(count($aDataJsonMovie->results) != 0)
        {

            // Get credit info from movie
            //----------------------------
            $aDataText 			= file_get_contents("http://api.themoviedb.org/3/movie/".$aDataJsonMovie->results[0]->id."/credits?api_key=".THEDBMOVIEKEY);
            $aDataJsonCredit 	= json_decode($aDataText);
            //////////////////////////////


            // Get general info from movie
            //----------------------------
            $aDataText 			= file_get_contents("http://api.themoviedb.org/3/movie/".$aDataJsonMovie->results[0]->id."?api_key=".THEDBMOVIEKEY);
            $aDataJsonMovie 	= json_decode($aDataText);
            //////////////////////////////



            // Test if movie already in DB
            //----------------------------
            $sQuerie 	= 'SELECT
								id_movie,
								name_file
							FROM
								movies
							WHERE
								name_file = "'.$aFichier[1].'"
							';
            $oResponse 	= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
            $aDataMovie = $oResponse->fetch();
            //////////////////////////////


            $iMovie = $aDataMovie['id_movie'];

            // if movie doesn't exist, add it
            //-------------------------------
            if(!isset($iMovie))
            {

                // Insert new movie in DB
                //-----------------------
                $sQuerie = 'INSERT INTO
								movies
							SET
								title			= "'.$aDataJsonMovie->title.'",
								release_date	= "'.$aDataJsonMovie->release_date.'",
								note			= "'.$aDataJsonMovie->vote_average.'",
								name_file		= "'.$aFichier[1].'",
								original_title	= "'.$aDataJsonMovie->original_title.'",
								runtime			= "'.$aDataJsonMovie->runtime.'",
								synopsis		= "'.addslashes($aDataJsonMovie->overview).'",
								poster_path		= "'.$aDataJsonMovie->poster_path.'",
                                file_path		= "'.$aFichier[2].utf8_encode($aFichier[1]).'"
							';

                ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                ////////////////////////////////////////////

                $iMovie = $oMyDB->lastInsertId();


                // Get actors info
                //----------------
                foreach($aDataJsonCredit->cast as $credit)
                {

                    // Split lastname and firstname
                    //-----------------------------
                    $firstnameName = explode(" ", $credit->name, strpos($credit->name, " "));


                    // Check in DB if actor is already in DB
                    //--------------------------------------
                    $sQuerie = 'SELECT
									id_actor
								FROM
									actors
								WHERE
									name = "'.$firstnameName[1].'"
								AND
									firstname = "'.$firstnameName[0].'"
								';

                    $oResponse	= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                    $aDataActor = $oResponse->fetch();
                    ////////////////////////////////////////////

                    $iActorId = $aDataActor['id_actor'];

                    // If actor doesn't exist, add it
                    //-------------------------------
                    if(!isset($iActorId))
                    {

                        // Insert actor
                        //-------------
                        $sQuerie = 'INSERT INTO
										actors
									SET
										name		= "'.$firstnameName[1].'",
										firstname	= "'.$firstnameName[0].'"
									';
                        ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                        ////////////////////////////////////////////


                        // Get last insert ID - NORMALEMENT A VERIFIER AUTREMENT FAIRE COMMENTAIRE
                        //--------------------------------------------
                        $iActorId = $oMyDB->lastInsertId();


                    }

                    // Check if actor is already in credit of movie
                    //---------------------------------------------
                    $sQuerie = 'SELECT
									id_movie,
									id_actor
								FROM
									movie_actors
								WHERE
									id_movie = "'.$iMovie.'"
								AND
									id_actor = "'.$iActorId.'"
								';

                    $oResponse			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                    $aDataMovieActor 	= $oResponse->fetch();
                    ////////////////////////////////////////////



                    // If doesn't exist, add it
                    //-------------------------
                    if(!isset($aDataMovieActor['id_actor']))
                    {

                        // Insert actor in movie
                        //----------------------
                        $sQuerie = 'INSERT INTO
										movie_actors
									SET
										id_movie	= "'.$iMovie.'",
										id_actor	= "'.$iActorId.'",
										role		= "'.addslashes($credit->character).'"';
                        ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                        ////////////////////////////////////////////

                    }
                }

                // Get type of movie
                //------------------
                foreach($aDataJsonMovie->genres as $genre)
                {

                    // Check if type is already in DB
                    //-------------------------------
                    $sQuerie = 'SELECT
									id_genre
								FROM
									genres
								WHERE
									genre = "'.$genre->name.'"';

                    $oResponse	= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                    $aDataGenre = $oResponse->fetch();
                    ////////////////////////////////////////////

                    $iGenre = $aDataGenre['id_genre'];


                    // If doesn't exist, insert in DB and get his id
                    //----------------------------------------------
                    if(!isset($aDataGenre['id_genre']))
                    {

                        // Insert type
                        //------------
                        $sQuerie = 'INSERT INTO
										genres
									SET
										genre = "'.$genre->name.'"';

                        $oResponse	= ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                        ////////////////////////////////////////////


                        // Get last insert ID
                        //-------------------
                        $iGenre = $oMyDB->lastInsertId();

                    }

                    // Check if in the movie the type is already insert or not
                    //--------------------------------------------------------
                    $sQuerie = 'SELECT
									id_movie,
									id_genre
								FROM
									movie_genres
								WHERE
									id_movie = "'.$iMovie.'"
								AND
									id_genre = "'.$iGenre.'"';

                    $oResponse			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                    $aDataMovieGenre 	= $oResponse->fetch();
                    ///////////////////////////////////////////


                    // If doesn't exist add it
                    //------------------------
                    if(!isset($aDataMovieGenre['id_genre']))
                    {

                        // Insert type
                        //------------
                        $sQuerie = 'INSERT INTO
										movie_genres
									SET
										id_movie	= "'.$iMovie.'",
										id_genre	= "'.$iGenre.'"';
                        ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                        ////////////////////////////////////////////

                    }
                }

                // Get producer and director info
                //-------------------------------
                foreach($aDataJsonCredit->crew as $crew)
                {

                    // Get producer
                    //-------------
                    if($crew->job == "Producer")
                    {

                        // Split lastname and firstname
                        //-----------------------------
                        $aFirstName = explode(" ", $crew->name, strpos($crew->name, " "));


                        // Check if producer is already in DB
                        //-----------------------------------
                        $sQuerie = 'SELECT
										id_producer
									FROM
										producers
									WHERE
										name 		= "'.$aFirstName[1].'"
									AND
										firstname 	= "'.$aFirstName[0].'"';

                        $oResponse 		= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                        $aDataProducer 	= $oResponse->fetch();
                        ////////////////////////////////////////////


                        $iProducer = $aDataProducer['id_producer'];

                        // If doesn't exist, add it and get his ID
                        //----------------------------------------
                        if(!isset($iProducer))
                        {

                            // Insert producer
                            //----------------
                            $sQuerie = 'INSERT INTO
											producers
										SET
											name		= "'.$aFirstName[1].'",
											firstname	= "'.$aFirstName[0].'"';

                            ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                            ////////////////////////////////////////////


                            // Get last insert ID - NORMALEMENT A VERIFIER AUTREMENT FAIRE COMMENTAIRE
                            //--------------------------------------------
                            $iProducer = $oMyDB->lastInsertId();

                        }


                        // Check if producer is already in movie or not
                        //---------------------------------------------
                        $sQuerie = 'SELECT
										id_movie,
										id_producer
									FROM
										movie_producers
									WHERE
										id_movie 	= "'.$iMovie.'"
									AND
										id_producer = "'.$iProducer.'"';

                        $oResponse 			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                        $aDataMovieProducer = $oResponse->fetch();
                        ////////////////////////////////////////////


                        // If producer is not referenced to the movie, add it
                        //---------------------------------------------------
                        if(!isset($aDataMovieProducer['id_producer']))
                        {

                            // Joint movie to producer
                            //------------------------
                            $sQuerie = 'INSERT INTO
											movie_producers
										SET
											id_movie	= "'.$iMovie.'",
											id_producer = "'.$iProducer.'"';

                            ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                            ////////////////////////////////////////////

                        }
                    }

                    // Get director info
                    //------------------
                    if($crew->job == "Director")
                    {

                        // Split lastname and firstname
                        //-----------------------------
                        $aFirstName = explode(" ", $crew->name, strpos($crew->name, " "));


                        // Check if director is already in DB
                        //-----------------------------------
                        $sQuerie = 'SELECT
										id_director
									FROM
										directors
									WHERE
										name = "'.$aFirstName[1].'"
									AND
										firstname = "'.$aFirstName[0].'"';

                        $oResponse 		= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                        $aDataDirector 	= $oResponse->fetch();
                        ////////////////////////////////////////////

                        $iDirector = $aDataDirector['id_director'];

                        // If doesn't exist, add in DB
                        //----------------------------
                        if(!isset($iDirector))
                        {

                            // Insert IN DB
                            //-------------
                            $sQuerie = 'INSERT INTO
											directors
										SET
											name		= "'.$aFirstName[1].'",
											firstname	= "'.$aFirstName[0].'"';

                            ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                            ////////////////////////////////////////////

                            // Get last insert ID - NORMALEMENT A VERIFIER AUTREMENT FAIRE COMMENTAIRE
                            //--------------------------------------------
                            $iDirector = $oMyDB->lastInsertId();
                        }


                        // If already exist
                        //-----------------
                        $sQuerie = 'SELECT
										id_movie,
										id_director
									FROM
										movie_directors
									WHERE
										id_movie = "'.$iMovie.'"
									AND
										id_director = "'.$iDirector.'"';

                        $oResponse 			= ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                        $aDataMovieDirector = $oResponse->fetch();
                        ////////////////////////////////////////////


                        // If doesn't exist, add it
                        //-------------------------
                        if(!isset($aDataMovieDirector['id_director']))
                        {

                            // Insert to the movie the director of it
                            //---------------------------------------
                            $sQuerie = 'INSERT INTO
											movie_directors
										SET
											id_movie		= "'.$iMovie.'",
											id_director		= "'.$iDirector.'"';

                            ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                            ////////////////////////////////////////////

                        }
                    }
                }
            }
        }
        else
        {

            // Insert in array all films doesn't found
            //----------------------------------------
            $aFileNotFoundTemp['correct'] 	= str_replace("+", " ", $aFichier[0]);
            $aFileNotFoundTemp['source'] 	= $aFichier[1];

            $aFileNotFound[] 				= $aFileNotFoundTemp;
        }


        // Let's time to themoviedb's server to respond
        //---------------------------------------------
        sleep(0.3);

    }

    return $aFileNotFound;
}



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


// Add movies who parse doesn't match
//-----------------------------------
function AddUnCorrectMovie($aFileNotFound)
{

    // Set variable
    //-------------
    $sMustInsert = "";


    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host='.MYSQLHOST.';dbname='.DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // String format
    //--------------
    foreach($aFileNotFound as $sFile)
    {
        $sMustInsert .= '("'.$sFile['source'].'","'.rtrim($sFile['correct']).'"),';
    }

    $sMustInsert = rtrim($sMustInsert, ",");
    //////////////////////////////////////////////////////////////


    // Insert uncorrect filename in DB if not already in
    //--------------------------------------------------
    $sQuerie = 'SELECT
                    id_correct
				FROM
                    corrects
				WHERE
                    filename_source = "'.$sFile['source'].'";';

    $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
    $aDataFile = $oResponse->fetch();
    //////////////////////////////////////////////////////////////


    // If the uncorrect movies isn't in DB, add it
    //--------------------------------------------
    if(!isset($aDataFile['id_correct']))
    {
        $sQuerie = 'INSERT INTO
					corrects
					(filename_source,
					filename_correct)
					VALUES ' .$sMustInsert;

        ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
    }
    ////////////////////////////////////////////
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


// ----------------------------------
// ExecuterRequetePreparee($connexion, $requete, $tab)
// Date de création :
// Date de révision :
// Auteur : Quentin Girard & Lola Olivet & Théo Zimmermann & Stéphane Martignier
// Entrée : la connexion à utiliser, la requete, les paramètres de la requete
// Sortie : le retour de la requête
// ----------------------------------
function ExecutePreparedQuerie($oMyDB, $sQuerie, $tab)
{
    $stmt = $oMyDB->prepare($sQuerie);

    $i = 0;
    while($i < count($tab))
    {
        $stmt->bindParam($i+1, $tab[$i]);
        $i++;
    }

    $stmt->execute();
    return $stmt;
}


// ----------------------------------
// ExecuterRequete($connexion, $requete, $typeRequete)
// Date de création :
// Date de révision :
// Auteur : Quentin Girard & Lola Olivet & Théo Zimmermann & Stéphane Martignier
// Entrée : la connexion à utiliser, la requete, le type de requête
// Sortie : le retour de la requête
// ----------------------------------
function ExecuteQuerie($oMyDB, $sQuerie, $typeQuerie)
{

    if ($typeQuerie == 'SELECT')
    {
        // print($sQuerie);
        $results = $oMyDB->query("$sQuerie"); //Exécution d'une requête SELECT
    }
    else
    {
         //print($sQuerie);
         // print("<br/>");
        $results = $oMyDB->exec("$sQuerie"); //Exécution d'une requête non SELECT
    }

    return ($results);
}



?>