<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script src="js/ffbad.js"></script>
        <meta name="description" content="">
        <meta name="author" content="">

        <title>FFBAD Player Finder</title>
        
        <link rel="icon" href="images/ffbad.png" />

        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="vendor/jqueryui/jquery-ui.min.css" rel="stylesheet">
        <link href="css/ffbad.css" rel="stylesheet">
        <link href="css/toast.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <style>
            body {
                padding-top: 54px;
            }
            @media (min-width: 992px) {
                body {
                    padding-top: 56px;
                }
            }

            #loader-container {
                text-align: center;
                display: block; 
            }

            .loader {
                display: inline-block;
                border: 10px solid #f3f3f3;
                border-radius: 50%;
                border-top: 10px solid #3498db;
                width: 80px;
                height: 80px;
                -webkit-animation: spin 2s linear infinite; /* Safari */
                animation: spin 2s linear infinite;
            }

            /* Safari */
            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>

        <!-- Scripts -->
        <script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
        <script defer src="js/toast.js"></script>

    </head>

    <body>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">FFBAD Player Finder</a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <a href="ws/exportPlayers.php?category=s" target="_blank" class="btn btn-success" role="button">Export Simple</a>
                    &nbsp;
                    <a href="ws/exportPlayers.php?category=d" target="_blank" class="btn btn-success" role="button">Export Double</a>
                    &nbsp;
                    <a href="ws/exportPlayers.php?category=m" target="_blank" class="btn btn-success" role="button">Export Mixed</a>
                    &nbsp;
                    <a href="javascript:reset();" class="btn btn-danger" role="button">Reset</a>
                </form>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container">
            <div class="row">

                <div class="col-lg-12 text-center">
                    <h4 class="my-3">Add a player</h4>
                    <!-- Form to update config file -->
                    <form class="form-row" action="javascript:addPlayer();">
                        <div class="col-8">
                            <input type="text" class="form-control mb-2 mr-sm-2" id="search" placeholder="Name / License" name="search">
                        </div>
                        <div class="col-3">
                            <select class="form-control" id="week" name="week">
                                <?php
                                foreach ($dateList as $week => $date) {
                                    echo '<option value="' . $week . '">' . $date . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-1">
                            <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Search</button>
                        </div>
                    </form>

                    <h4 class="my-3">Players</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">License</th>
                                <th scope="col">Name</th>
                                <th scope="col">Age</th>
                                <th scope="col">S</th>
                                <th scope="col">D</th>
                                <th scope="col">M</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="players">

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div id="loader-container">
            <div class="loader"></div>
        </div>
        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/jqueryui/jquery-ui.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                updatePlayersList();
                $("#players").sortable({
                    placeholder: "ui-state-highlight",
                    axis: "y"
                });
                $("#players").disableSelection();
                $("#players").sortable({
                    update: function (event, ui) {
                        updatePlayersOrder();
                    }
                });
            });
        </script>

    </body>

</html>
