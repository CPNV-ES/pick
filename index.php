<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Pick.</title>

        <!-- Bootstrap -->
        <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="./assets/css/style.css" rel="stylesheet">
        <link href="./assets/css/animate.css" rel="stylesheet">

        <!-- JavaScript -->
        <script src="./assets/js/jquery-2.1.4.min.js"></script>
        <script src="./assets/js/bootstrap.min.js"></script>
        <script src="./src/scripts/functions.js"></script>
        <script src="./assets/js/filters.js"></script>
        <script src="./assets/js/rotate-min.js"></script>



        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Est ce que ça marche ? -->

    </head>
    <body>
        <div id="overlay" class="modal fade in" style="margin-top:17%; margin-left:6%;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="loading" class="overlay-message">
                        <ul class="bokeh">
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <h3>SCAN IN PROGRESS, PLEASE WAIT</h3>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dalog -->
        </div><!-- /.modal -->


        <!-- MODAL POUR DESCRIPTION -->
        <div class="modal fade in" id="modalplus">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <p id="modalcontent"></p>
                    </div>
                </div>
            </div>
        </div>


        <div id="page">
            <div class="container">
                <div class="row row-centered">
                    <div id="correct_string" class="col-xs-6 col-centered animated fadeInDown">
                        <div class="form-group correct">
                            <p id="notfound" class="p.correct"></p>
                            <!-- BUTTON TRIGGER MODAL -->
                            <a id="modal" href="#" class="tr animated bounceIn">CORRECT !</a></p>
                    </div>
                </div>
            </div>


            <!-- SCAN PART ------------------------------>
            <div class="col-xs-3 col-lg-3 scan">
                <a href="#" id="lScan" class="tr animated flipInX">SCAN</a>

            </div>
            <div class="col-xs-3 col-centered">
                <img src="./assets/img/logo.png" class="logo" alt="pick_logo" height="auto" width="220px">
            </div>
            <div id="" class="col-xs-6 col-centered baseline">
                feelin' bored ?
            </div>

            <!-- SEARCH BAR TITLE-->
            <div class="col-xs-6 col-centered">
                <form class="form-inline global-search" role="form">
                    <div class="form-group">

                        <label class="sr-only" for="">Enter any movie title..</label>
                        <input type="search" class="form-control" id="k" name="k" placeholder="Enter any movie title..">
                        <input id="cn" name="cn" type="hidden" value="false" />
                        <input id="btn" name="cn" type="button" value="" />
                    </div>

                </form>
            </div>
            <!-- END - SEARCH BAR TITLE-->


			<!-- SEARCH BAR HIDDEN-->
            <div class="col-xs-6 col-centered" id="filter_menu">
                <ul>
                    <a href="#" id="y_link" class="trl"><li class="col-xs-4 animated slideInLeft tr">Year</li></a>
                    <a href="#" id="g_link"  class="trl"><li class="col-xs-4 animated slideInUp tr">Genre</li></a>
                    <a href="#" id="a_link"  class="trl"><li class="col-xs-4 animated slideInRight tr">Actors</li></a>
                </ul>

                <div class="col-centered">
                    <form class="fplus"role="form">
                        <div class="form-group">
                            <input type="search" class="form-control" id="y" name="y" placeholder="By year">
                            <span id="genre"></span>
                            <!--<input type="search" class="form-control" id="g" name="g" placeholder="By genre">-->
                            <input type="search" class="form-control" id="a" name="a" placeholder="By actors">
                            <input id="cn" name="cn" type="hidden" value="false" />
                        </div>
                    </form>
                </div>
            </div>
			<!-- END - SEARCH BAR -->


            <div class="col-xs-12 col-lg-12 col-centered" id="list">
            </div>
        </div>
        </div>
    </div>


</body>
</html>