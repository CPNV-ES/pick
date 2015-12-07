<?php
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


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Search information in TheMovieDB
//---------------------------------
function SearchInMovieDB($aFile)
{

    // Set variable
    //-------------
    $aFileNotFound = "";

    foreach ($aFile as $sFichier)
    {

        // Split correct file name and source file name
        //---------------------------------------------
        $aFichier = explode(";", $sFichier);

        // Try to find the film in themoviedb's DB
        //----------------------------------------
        $aDataText = file_get_contents("http://api.themoviedb.org/3/search/movie?api_key=" . THEDBMOVIEKEY . "&query=" . $aFichier[0]);

        // Return JSON. Must decode
        //-------------------------
        $aDataJsonMovie = json_decode($aDataText);

        // Check if the name of the file was found in themoviedb
        //------------------------------------------------------
        if (count($aDataJsonMovie -> results) != 0)
        {

            InsertInDB($aDataJsonMovie -> results[0] -> id, $aFichier[1]);

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


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Insert movies un our DB
//------------------------
function InsertInDB($idMovie, $aFichier)
{

    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host=' . MYSQLHOST . ';dbname=' . DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


    // Get credit info from movie
    //----------------------------
    $aDataText = file_get_contents("http://api.themoviedb.org/3/movie/" . $idMovie . "/credits?api_key=" . THEDBMOVIEKEY);
    $aDataJsonCredit = json_decode($aDataText);
    //////////////////////////////


    // Get general info from movie
    //----------------------------
    $aDataText = file_get_contents("http://api.themoviedb.org/3/movie/" . $idMovie . "?api_key=" . THEDBMOVIEKEY);
    $aDataJsonMovie = json_decode($aDataText);
    //////////////////////////////


    // Test if movie already in DB
    //----------------------------
    $sQuerie = 'SELECT
					id_movie,
					name_file
				FROM
					movies
				WHERE
					name_file = "' . $aFichier . '"';

    $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
    $aDataMovie = $oResponse -> fetch();
    //////////////////////////////


    $iMovie = $aDataMovie['id_movie'];


    // if movie doesn't exist, add it
    //-------------------------------
    if (!isset($iMovie))
    {

        // Insert new movie in DB
        //-----------------------
        $sQuerie = 'INSERT INTO
						movies
					SET
						title			= "' . $aDataJsonMovie -> title . '",
						release_date	= "' . $aDataJsonMovie -> release_date . '",
						note			= "' . $aDataJsonMovie -> vote_average . '",
						name_file		= "' . $aFichier . '",
						original_title	= "' . $aDataJsonMovie -> original_title . '",
						runtime			= "' . $aDataJsonMovie -> runtime . '",
						synopsis		= "' . addslashes($aDataJsonMovie -> overview) . '",
						poster_path		= "' . $aDataJsonMovie -> poster_path . '",
						file_path		= "' . USERFOLDER .'/'. utf8_encode($aFichier) . '"';

        ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
        ////////////////////////////////////////////


         $iMovie = $oMyDB -> lastInsertId();


        // Get actors info
        //----------------
        foreach ($aDataJsonCredit->cast as $credit)
        {

            // Split lastname and firstname
            //-----------------------------
            $firstnameName = explode(" ", $credit -> name, strpos($credit -> name, " "));


            // Check in DB if actor is already in DB
            //--------------------------------------
            $sQuerie = 'SELECT
							id_actor
						FROM
							actors
						WHERE
							name = "' . ((isset($firstnameName[1])) ? $firstnameName[1] : '') . '"
						AND
							firstname = "' . $firstnameName[0] . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
            $aDataActor = $oResponse -> fetch();

            ////////////////////////////////////////////

            $iActorId = $aDataActor['id_actor'];

            // If actor doesn't exist, add it
            //-------------------------------
            if (!isset($iActorId))
            {

                // Insert actor
                //-------------
                $sQuerie = 'INSERT INTO
								actors
							SET
								name		= "' . ((isset($firstnameName[1])) ? $firstnameName[1] : '') . '",
								firstname	= "' . $firstnameName[0] . '"';

                ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                ////////////////////////////////////////////

                // Get last insert ID
                //-------------------
                $iActorId = $oMyDB -> lastInsertId();

            }

            // Check if actor is already in credit of movie
            //---------------------------------------------
            $sQuerie = 'SELECT
							 id_movie,
							 id_actor
						FROM
							 movie_actors
						 WHERE
							 id_movie = "' . $iMovie . '"
						 AND
							 id_actor = "' . $iActorId . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
            $aDataMovieActor = $oResponse -> fetch();
            ////////////////////////////////////////////


            // If doesn't exist, add it
            //-------------------------
            if (!isset($aDataMovieActor['id_actor']))
            {

                // Insert actor in movie
                //----------------------
                $sQuerie = 'INSERT INTO
								 movie_actors
							SET
								 id_movie	= "' . $iMovie . '",
								 id_actor	= "' . $iActorId . '",
								 role		= "' . addslashes($credit -> character) . '"';

                ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                ////////////////////////////////////////////

            }
        }

        // Get type of movie
        //------------------
        foreach ($aDataJsonMovie->genres as $genre)
        {

            // Check if type is already in DB
            //-------------------------------
            $sQuerie = 'SELECT
							 id_genre
						FROM
							 genres
						 WHERE
							 genre = "' . $genre -> name . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
            $aDataGenre = $oResponse -> fetch();
            ////////////////////////////////////////////

            $iGenre = $aDataGenre['id_genre'];

            // If doesn't exist, insert in DB and get his id
            //----------------------------------------------
            if (!isset($aDataGenre['id_genre']))
            {

                // Insert type
                //------------
                $sQuerie = 'INSERT INTO
								genres
							SET
								genre = "' . $genre -> name . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                ////////////////////////////////////////////

                // Get last insert ID
                //-------------------
                $iGenre = $oMyDB -> lastInsertId();

			}

            // Check if in the movie the type is already insert or not
            //--------------------------------------------------------
            $sQuerie = 'SELECT
							id_movie,
							id_genre
						FROM
							movie_genres
						WHERE
							id_movie = "' . $iMovie . '"
						AND
							id_genre = "' . $iGenre . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
            $aDataMovieGenre = $oResponse -> fetch();
            ///////////////////////////////////////////


            // If doesn't exist add it
            //------------------------
            if (!isset($aDataMovieGenre['id_genre']))
            {

                // Insert type
                //------------
                $sQuerie = 'INSERT INTO
								movie_genres
							SET
								id_movie	= "' . $iMovie . '",
								id_genre	= "' . $iGenre . '"';

                ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                ////////////////////////////////////////////

            }
        }

        // Get producer and director info
        //-------------------------------
        foreach ($aDataJsonCredit->crew as $crew)
        {

            // Get producer
            //-------------
            if ($crew -> job == "Producer")
            {

                // Split lastname and firstname
                //-----------------------------
                $aFirstName = explode(" ", $crew -> name, strpos($crew -> name, " "));

                // Check if producer is already in DB
                //-----------------------------------
                $sQuerie = 'SELECT
								id_producer
							FROM
								producers
							WHERE
								name 		= "' . $aFirstName[1] . '"
							AND
								firstname 	= "' . $aFirstName[0] . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                $aDataProducer = $oResponse -> fetch();
                ////////////////////////////////////////////

                $iProducer = $aDataProducer['id_producer'];

                // If doesn't exist, add it and get his ID
                //----------------------------------------
                if (!isset($iProducer))
                {

                    // Insert producer
                    //----------------
                    $sQuerie = 'INSERT INTO
									producers
								SET
									name		= "' . $aFirstName[1] . '",
									firstname	= "' . $aFirstName[0] . '"';

                    ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                    ////////////////////////////////////////////

                    // Get last insert ID
                    //-------------------
                    $iProducer = $oMyDB -> lastInsertId();

                }

                // Check if producer is already in movie or not
                //---------------------------------------------
                $sQuerie = 'SELECT
								id_movie,
								id_producer
							FROM
								movie_producers
							WHERE
								id_movie 	= "' . $iMovie . '"
							AND
								id_producer = "' . $iProducer . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                $aDataMovieProducer = $oResponse -> fetch();
                ////////////////////////////////////////////

                // If producer is not referenced to the movie, add it
                //---------------------------------------------------
                if (!isset($aDataMovieProducer['id_producer']))
                {

                    // Joint movie to producer
                    //------------------------
                    $sQuerie = 'INSERT INTO
									movie_producers
								SET
									id_movie	= "' . $iMovie . '",
									id_producer = "' . $iProducer . '"';

                    ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                    ////////////////////////////////////////////

                }
            }

            // Get director info
            //------------------
            if ($crew -> job == "Director")
            {

                // Split lastname and firstname
                //-----------------------------
                $aFirstName = explode(" ", $crew -> name, strpos($crew -> name, " "));


                // Check if director is already in DB
                //-----------------------------------
                $sQuerie = 'SELECT
                                id_director
                            FROM
                                directors
                            WHERE
                                name = "' . $aFirstName[1] . '"
                            AND
                                firstname = "' . $aFirstName[0] . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                $aDataDirector = $oResponse -> fetch();
                ////////////////////////////////////////////

                $iDirector = $aDataDirector['id_director'];

                // If doesn't exist, add in DB
                //----------------------------
                if (!isset($iDirector))
                {

                    // Insert IN DB
                    //-------------
                    $sQuerie = 'INSERT INTO
                                    directors
                                SET
                                    name		= "' . $aFirstName[1] . '",
                                    firstname	= "' . $aFirstName[0] . '"';

                    ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                    ////////////////////////////////////////////

                    // Get last insert ID
                    //-------------------
                    $iDirector = $oMyDB -> lastInsertId();
                }

                // If already exist
                //-----------------
                $sQuerie = 'SELECT
                                id_movie,
                                id_director
                            FROM
                                movie_directors
                            WHERE
                                id_movie = "' . $iMovie . '"
                            AND
                                id_director = "' . $iDirector . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
                $aDataMovieDirector = $oResponse -> fetch();
                ////////////////////////////////////////////

                // If doesn't exist, add it
                //-------------------------
                if (!isset($aDataMovieDirector['id_director']))
                {

                    // Insert to the movie the director of it
                    //---------------------------------------
                    $sQuerie = 'INSERT INTO
                                    movie_directors
                                SET
                                    id_movie		= "' . $iMovie . '",
                                    id_director		= "' . $iDirector . '"';

                    ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
                    ////////////////////////////////////////////

                }
            }
        }
    }
 }



// Execute a new search about uncorrect movies with change of user
//----------------------------------------------------------------
function SearchCorrectedMoviesInMovieDB()
{

    // Connection to DB
    //-----------------
    $oMyDB = new PDO('mysql:host=' . MYSQLHOST . ';dbname=' . DATABASE, MYSQLUSER, MYSQLPWD);
    $oMyDB -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //////////////////////////////////////////////////////////////


	// Parse $_POST array with new movie's name
	//-----------------------------------------
    for($i = 0; $max = count($_POST)/3, $i < $max; $i++)
    {

        // Try to find the film in themoviedb's DB
        //----------------------------------------
        $aDataText = file_get_contents("http://api.themoviedb.org/3/search/movie?api_key=" . THEDBMOVIEKEY . "&query=" . str_replace(' ', '+', $_POST['nameCorrect'.$i]));


        // Return JSON. Must decode
        //-------------------------
        $aDataJsonMovie = json_decode($aDataText);


        // Check if the name of the file was found in themoviedb
        //------------------------------------------------------
        if (count($aDataJsonMovie -> results) != 0)
        {

			// Insert new movie in our DB
			//---------------------------
            InsertInDB($aDataJsonMovie -> results[0] -> id, $_POST['nameFile'.$i]);


			// Remove the movie found in table corrects
			//-----------------------------------------
            $sQuerie = "DELETE FROM
            				corrects
            			WHERE
            				id_correct =".$_POST['id'.$i].";";

            ExecuteQuerie($oMyDB, $sQuerie, 'DELETE');
        }


        // Let's time to themoviedb's server to respond
        //---------------------------------------------
        sleep(0.3);
    }
}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Add movies who parse doesn't match
//-----------------------------------
function AddUnCorrectMovie($aFileNotFound)
{

	// Set variable
	//-------------
	$sMustInsert = "";


	// Connection to DB
	//-----------------
	$oMyDB = new PDO('mysql:host=' . MYSQLHOST . ';dbname=' . DATABASE, MYSQLUSER, MYSQLPWD);
	$oMyDB -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//////////////////////////////////////////////////////////////


	// String format
	//--------------
	foreach ($aFileNotFound as $sFile)
	{
		$sMustInsert .= '("' . $sFile['source'] . '","' . rtrim($sFile['correct']) . '"),';
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
                    filename_source = "' . $sFile['source'] . '";';

	$oResponse = ExecuteQuerie($oMyDB, $sQuerie, 'SELECT');
	$aDataFile = $oResponse -> fetch();
	//////////////////////////////////////////////////////////////


	// If the uncorrect movies isn't in DB, add it
	//--------------------------------------------
	if (!isset($aDataFile['id_correct']))
	{
		$sQuerie = 'INSERT INTO
					corrects
					(filename_source,
					filename_correct)
					VALUES ' . $sMustInsert;

		ExecuteQuerie($oMyDB, $sQuerie, 'INSERT');
	}
	////////////////////////////////////////////
}

