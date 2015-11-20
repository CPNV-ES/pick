<?php
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
        $aReplace = array (
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
                            " "
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

					// Doesn't go in directory who containt (saison or season)
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
                    $sPath = str_replace("/", "\\", $sDir . DIRECTORY_SEPARATOR . $sValue);
                    $sPath = str_replace("\\\\", "\\", $sPath);


                    // Encode for correct visibility
                    //------------------------------
                    $sFichierTemp   = str_ireplace($aReplace, "+", utf8_encode($sValue));


					// Insert films
					//-------------
					$aReturn[] .= $sFichierTemp.";".$sValue;

                }
            }
        }

        return $aReturn;
    }

	// Call function to parse user directory
	//--------------------------------------
    $aPrint = dirToArray('/xampp/htdocs/Projet/Video/');


	// Let's see all films find
	//-------------------------
	foreach($aPrint as $sFichier)
	{

		// Split correct file name and source file name
		//---------------------------------------------
		$aFichier = explode(";", $sFichier);


		// Try to find the film in themoviedb's DB
		//----------------------------------------
        $aDataText = file_get_contents("http://api.themoviedb.org/3/search/movie?api_key=db663b344723dd2d6781aed1e2f7764d&query=".$aFichier[0]);


		// Return JSON so must decode
		//---------------------------
        $aDataJsonMovie = json_decode($aDataText);


		// Check if the name of the file was found in themoviedb
		//------------------------------------------------------
		if(count($aDataJsonMovie->results) != 0)
		{

			// Execution fonction de Stéphane
			//-------------------------------
			print("<hr>");
			print("<h1>".$aFichier[0]." = ".$aDataJsonMovie->results[0]->title."</h1>");
		}
		else
		{

			// Générer tableau avec film non trouvé
			//-------------------------------------
			print("<hr>");
			print("Le film ". $aFichier[0] ." n'as pas été trouvé !");
		}


		// Let's time to themoviedb's server to respond
		//---------------------------------------------
		sleep(0.2);

	}
?>