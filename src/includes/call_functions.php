<?php
include("./functions.inc.php");

switch($_GET['todo'])
{
    case 'scan':

        $aMovieFromDisk = dirToArray('/xampp/htdocs/sweg/Video_light/');
        $aMovieNotFound = SearchAndInsert($aMovieFromDisk);

        // print($aMovieNotFound);

        AddUnCorrectMovie($aMovieNotFound);



        // Call function to get film and print it
        //---------------------------------------
        $aMovie = GetAllMovies();
        $sPrint = "";

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

        break;

    case 'moviesnotfound':

        $aNbMoviesNotFound = GetNumberUnCorrectMovie();

        print("You have ".$aNbMoviesNotFound['NbMoviesNotFound']." movie(s) who doesn't match");

        break;


    case 'displaymodalcorrect':

        $aUnCorrectMovies = GetUnCorrectMovie();
        $sPrint = "";

        foreach($aUnCorrectMovies as $sMovies)
        {
            $sPrint .="<div class='form-group'><input type='text' class='form-control' placeholder='".$sMovies['filename_correct']."' value='".$sMovies['filename_correct']."'></div>";
        }

		$sPrint .= '
			<div class="modal-footer">
				<div class="btn-group">
					<button class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
					<button id="correctmovie" class="btn btn-primary"><span class="glyphicon glyphicon-check"></span> Save</button>
				</div>
			</div>';

        print($sPrint);


        break;

    case 'showDetails' :

        $aData = GetFilmDescription($_GET['id']);

        $sPrint ="
                    <h1>".$aData[0]['title']."</h1>
                    <div class='col-md-8'>
            <img src='".$aData[0]['poster_path']."' />
            </div>

                    <div class='col-md-3'>
                   <span class='glyphicon glyphicon-calendar' aria-hidden='true'></span> ".date("d.m.Y", strtotime($aData[0]['release']))." <br />
                   <span class='glyphicon glyphicon-time' aria-hidden='true'></span> ".$aData[0]['runtime']." min <br />
                   <h2>Actors</h2>";

                           foreach ($aData[0]['actors'] as $aActor)
                           {
                                  $sPrint .= $aActor['firstname']." ".$aActor['name']."<br/>";
                           }

              $sPrint .= "
              </div>
                    <div class='col-md-12'>
                           <span class='glyphicon glyphicon-file' aria-hidden='true'></span> <span class='small'>".$aData[0]['file_path']."</span>
                   <h2>Synopsis</h2>
                   ".$aData[0]['synopsis']."
            </div><div class='clearfix'></div>";

        print($sPrint);


        break;


    case 'getallmovies':

        // Call function to get film and print it
        //---------------------------------------
        $aMovie = GetAllMovies();
        $sPrint = "";

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

		break;


case 'searchMovies':

	$aMovie = GetSearchMovies($_GET['queryT'], $_GET['queryA'], $_GET['queryG'], $_GET['queryY']);
    $sPrint = "";

	if(count($aMovie) == 0 && ($_GET['queryT'] != "" || $_GET['queryA'] != "" || $_GET['queryG'] != "" || $_GET['queryY'] != ""))
	{
		$sPrint = "Aucun film trouvé";
	}
	else if(count($aMovie) == 0 && $_GET['queryT'] == "" && $_GET['queryA'] == "" && $_GET['queryG'] == "" && $_GET['queryY'] == "")
	{
		$aMovie = GetAllMovies();
	}
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

	break;

}

?>