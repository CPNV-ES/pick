<?php
set_time_limit(240);

// ----------------------------------
// DirToArray($sDir)
// Param 		: $sDir -> user path
// Description	: Scan user directory,
//				parse and try to clear filename
// ----------------------------------
function DirToArray($sDir)
{

    // Variable
    //---------
    $aResult	= [];
    $aReturn	= [];
	////////////////////////////////////////////


	// Check is_dir
	//-------------
	if (is_dir($sDir))
	{
		$aDir	= scandir($sDir);
	}
	else
	{
		die ("<div id='notfoundcontent'>CHECK THE FILE PATH OF YOUR DIRECTORY IN <kbd>src/config/config.php</kbd></div>");
	}
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
            if (is_dir($sDir . DIRECTORY_SEPARATOR . $sValue))
            {

                // Doesn't go in directory that contains (saison or season)
                //--------------------------------------------------------
                if (!preg_match_all("#(saison|season)#i", $sValue))
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


				// If preg_match found nothink
				//-----------------------------
				if (!isset($matches[2]))
				{

					// If regex doesn't match replace caracter
					//----------------------------------------
					$sValueParse = str_ireplace($aReplace2, " ", $sValue);
				}
				else
				{

					// Match of the regex
					//-------------------
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


// ----------------------------------
// SearchInMovieDB($aFile)
// Param 		: $aFile
//					0 -> New filename
//					1 -> Source filename
//					2 -> Path
// Description	: Try to find movies in themoviedb
//				OK go to InsertInDB()
//				KO return array
// ----------------------------------
function SearchInMovieDB($aFile)
{

    // Set variable
    //-------------
    $aFileNotFound = "";

    foreach ($aFile as $aFileTemp)
    {

        // Split correct file name and source file name
        //---------------------------------------------
        $aSplitFile = explode(";", $aFileTemp);


        // Try to find the film in themoviedb's DB
        //----------------------------------------
        $aDataText = file_get_contents("http://api.themoviedb.org/3/search/movie?api_key=" . THEDBMOVIEKEY . "&query=" . $aSplitFile[0]);


        // Return JSON. Must decode
        //-------------------------
        $aDataJsonMovie = json_decode($aDataText);


        // Check if the name of the file was found in themoviedb
        //------------------------------------------------------
        if (count($aDataJsonMovie -> results) != 0)
        {
            InsertInDB($aDataJsonMovie -> results[0] -> id, $aSplitFile[1]);
        }
        else
        {

            // Insert in array all films doesn't found
            //----------------------------------------
            $aFileNotFoundTemp['correct'] 	= str_replace("+", " ", $aSplitFile[0]);
            $aFileNotFoundTemp['source'] 	= $aSplitFile[1];

            $aFileNotFound[] 				= $aFileNotFoundTemp;
        }

        // Let's time to themoviedb's server to respond
        //---------------------------------------------
        sleep(0.3);
    }

    return $aFileNotFound;
}



// ----------------------------------
// InsertInDB($idMovie, $sFileName)
// Param		: $idMovie 		-> id from themoviedb
//				: $sFileName 	-> New filename
// Description	: Get informations about movies by
//				themoviedb DB and insert in our DB
// ----------------------------------
function InsertInDB($idMovie, $sFileName)
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
    $sQuery = 'SELECT
					id_movie,
					name_file
				FROM
					movies
				WHERE
					name_file = "' . $sFileName . '"';

    $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
    $aDataMovie = $oResponse -> fetch();
    //////////////////////////////


    $iMovie = $aDataMovie['id_movie'];


    // if movie doesn't exist, add it
    //-------------------------------
    if (!isset($iMovie))
    {

        // Insert new movie in DB
        //-----------------------
        $sQuery = 'INSERT INTO
						movies
					SET
						title			= "' . $aDataJsonMovie -> title . '",
						release_date	= "' . $aDataJsonMovie -> release_date . '",
						note			= "' . $aDataJsonMovie -> vote_average . '",
						name_file		= "' . $sFileName . '",
						original_title	= "' . $aDataJsonMovie -> original_title . '",
						runtime			= "' . $aDataJsonMovie -> runtime . '",
						synopsis		= "' . addslashes($aDataJsonMovie -> overview) . '",
						poster_path		= "' . $aDataJsonMovie -> poster_path . '",
						file_path		= "' . USERFOLDER .'/'. utf8_encode($sFileName) . '"';

        ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
        ////////////////////////////////////////////


         $iMovie = $oMyDB -> lastInsertId();


        // Get actors info
        //----------------
        foreach ($aDataJsonCredit->cast as $credit)
        {

            // Split lastname and firstname
            //-----------------------------
            $aNameActor = explode(" ", $credit -> name, strpos($credit -> name, " "));

			if (count($aNameActor) > 2)
			{
				for($i = 1;$aNameActor <= count($aNameActor); $i++)
				{
					$aNameActor[1] = $aNameActor[1]." ".$aNameActor[$i];
				}

			}

            // Check in DB if actor is already in DB
            //--------------------------------------
            $sQuery = 'SELECT
							id_actor
						FROM
							actors
						WHERE
							name = "' . ((isset($aNameActor[1])) ? $aNameActor[1] : '') . '"
						AND
							firstname = "' . $aNameActor[0] . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
            $aDataActor = $oResponse -> fetch();

            ////////////////////////////////////////////

            $iActorId = $aDataActor['id_actor'];

            // If actor doesn't exist, add it
            //-------------------------------
            if (!isset($iActorId))
            {

                // Insert actor
                //-------------
                $sQuery = 'INSERT INTO
								actors
							SET
								name		= "' . ((isset($aNameActor[1])) ? $aNameActor[1] : '') . '",
								firstname	= "' . $aNameActor[0] . '"';

                ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
                ////////////////////////////////////////////

                // Get last insert ID
                //-------------------
                $iActorId = $oMyDB -> lastInsertId();

            }

            // Check if actor is already in credit of movie
            //---------------------------------------------
            $sQuery = 'SELECT
							 id_movie,
							 id_actor
						FROM
							 movie_actors
						 WHERE
							 id_movie = "' . $iMovie . '"
						 AND
							 id_actor = "' . $iActorId . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
            $aDataMovieActor = $oResponse -> fetch();
            ////////////////////////////////////////////


            // If doesn't exist, add it
            //-------------------------
            if (!isset($aDataMovieActor['id_actor']))
            {

                // Insert actor in movie
                //----------------------
                $sQuery = 'INSERT INTO
								 movie_actors
							SET
								 id_movie	= "' . $iMovie . '",
								 id_actor	= "' . $iActorId . '",
								 role		= "' . addslashes($credit -> character) . '"';

                ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
                ////////////////////////////////////////////

            }
        }

        // Get type of movie
        //------------------
        foreach ($aDataJsonMovie->genres as $genre)
        {

            // Check if type is already in DB
            //-------------------------------
            $sQuery = 'SELECT
							 id_genre
						FROM
							 genres
						 WHERE
							 genre = "' . $genre -> name . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
            $aDataGenre = $oResponse -> fetch();
            ////////////////////////////////////////////

            $iGenre = $aDataGenre['id_genre'];

            // If doesn't exist, insert in DB and get his id
            //----------------------------------------------
            if (!isset($aDataGenre['id_genre']))
            {

                // Insert type
                //------------
                $sQuery = 'INSERT INTO
								genres
							SET
								genre = "' . $genre -> name . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
                ////////////////////////////////////////////

                // Get last insert ID
                //-------------------
                $iGenre = $oMyDB -> lastInsertId();

			}

            // Check if in the movie the type is already insert or not
            //--------------------------------------------------------
            $sQuery = 'SELECT
							id_movie,
							id_genre
						FROM
							movie_genres
						WHERE
							id_movie = "' . $iMovie . '"
						AND
							id_genre = "' . $iGenre . '"';

            $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
            $aDataMovieGenre = $oResponse -> fetch();
            ///////////////////////////////////////////


            // If doesn't exist add it
            //------------------------
            if (!isset($aDataMovieGenre['id_genre']))
            {

                // Insert type
                //------------
                $sQuery = 'INSERT INTO
								movie_genres
							SET
								id_movie	= "' . $iMovie . '",
								id_genre	= "' . $iGenre . '"';

                ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
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
                $sQuery = 'SELECT
								id_producer
							FROM
								producers
							WHERE
								name 		= "' . $aFirstName[1] . '"
							AND
								firstname 	= "' . $aFirstName[0] . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
                $aDataProducer = $oResponse -> fetch();
                ////////////////////////////////////////////

                $iProducer = $aDataProducer['id_producer'];

                // If doesn't exist, add it and get his ID
                //----------------------------------------
                if (!isset($iProducer))
                {

                    // Insert producer
                    //----------------
                    $sQuery = 'INSERT INTO
									producers
								SET
									name		= "' . $aFirstName[1] . '",
									firstname	= "' . $aFirstName[0] . '"';

                    ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
                    ////////////////////////////////////////////

                    // Get last insert ID
                    //-------------------
                    $iProducer = $oMyDB -> lastInsertId();

                }

                // Check if producer is already in movie or not
                //---------------------------------------------
                $sQuery = 'SELECT
								id_movie,
								id_producer
							FROM
								movie_producers
							WHERE
								id_movie 	= "' . $iMovie . '"
							AND
								id_producer = "' . $iProducer . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
                $aDataMovieProducer = $oResponse -> fetch();
                ////////////////////////////////////////////


                // If producer is not referenced to the movie, add it
                //---------------------------------------------------
                if (!isset($aDataMovieProducer['id_producer']))
                {

                    // Joint movie to producer
                    //------------------------
                    $sQuery = 'INSERT INTO
									movie_producers
								SET
									id_movie	= "' . $iMovie . '",
									id_producer = "' . $iProducer . '"';

                    ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
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
                $sQuery = 'SELECT
                                id_director
                            FROM
                                directors
                            WHERE
                                name = "' . $aFirstName[1] . '"
                            AND
                                firstname = "' . $aFirstName[0] . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
                $aDataDirector = $oResponse -> fetch();
                ////////////////////////////////////////////

                $iDirector = $aDataDirector['id_director'];

                // If doesn't exist, add in DB
                //----------------------------
                if (!isset($iDirector))
                {

                    // Insert IN DB
                    //-------------
                    $sQuery = 'INSERT INTO
                                    directors
                                SET
                                    name		= "' . $aFirstName[1] . '",
                                    firstname	= "' . $aFirstName[0] . '"';

                    ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
                    ////////////////////////////////////////////

                    // Get last insert ID
                    //-------------------
                    $iDirector = $oMyDB -> lastInsertId();
                }


                // If already exist
                //-----------------
                $sQuery = 'SELECT
                                id_movie,
                                id_director
                            FROM
                                movie_directors
                            WHERE
                                id_movie = "' . $iMovie . '"
                            AND
                                id_director = "' . $iDirector . '"';

                $oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
                $aDataMovieDirector = $oResponse -> fetch();
                ////////////////////////////////////////////


                // If doesn't exist, add it
                //-------------------------
                if (!isset($aDataMovieDirector['id_director']))
                {

                    // Insert to the movie the director of it
                    //---------------------------------------
                    $sQuery = 'INSERT INTO
                                    movie_directors
                                SET
                                    id_movie		= "' . $iMovie . '",
                                    id_director		= "' . $iDirector . '"';

                    ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
                    ////////////////////////////////////////////

                }
            }
        }
    }
 }



// ----------------------------------
// SearchCorrectedMoviesInMovieDB()
// Description	: Execute a new search about
//				uncorrect movies with change of user
// ----------------------------------
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
            $sQuery = "DELETE FROM
            				corrects
            			WHERE
            				id_correct =".$_POST['id'.$i].";";

            ExecuteQuerie($oMyDB, $sQuery, 'DELETE');
        }


        // Let's time to themoviedb's server to respond
        //---------------------------------------------
        sleep(0.3);
    }
}



// ----------------------------------
// AddUnCorrectMovie($aFileNotFound)
// Param		: $aFileNotFound -> source filename and result of parse by scan
// Description	: Add movies who parse doesn't match
// ----------------------------------
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
	$sQuery = 'SELECT
                    id_correct
				FROM
                    corrects
				WHERE
                    filename_source = "' . $sFile['source'] . '";';

	$oResponse = ExecuteQuerie($oMyDB, $sQuery, 'SELECT');
	$aDataFile = $oResponse -> fetch();
	//////////////////////////////////////////////////////////////


	// If the uncorrect movies isn't in DB, add it
	//--------------------------------------------
	if (!isset($aDataFile['id_correct']))
	{
		$sQuery = 'INSERT INTO
					corrects
					(filename_source,
					filename_correct)
					VALUES ' . $sMustInsert;

		ExecuteQuerie($oMyDB, $sQuery, 'INSERT');
	}
	////////////////////////////////////////////
}



// ----------------------------------
// VerifConnection()
// Description	: Check if user have internet
// ----------------------------------
function CheckConnection()
{
	$bConnected = @fsockopen("www.goggle.com", 80);

    if ($bConnected)
    {

		fclose($bConnected);

        return true;

    }
    else
    {

        return false;
    }


}

