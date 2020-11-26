<?php

include_once 'database/Connection.php';
include_once 'controllers/FilmController.php';

use database\Connection;
use controllers\FilmController;
use repositories\FilmRepository;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <script href="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link href="/css/style.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="#"/>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="/">WebbyLabFilms</a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<br><br><br>
<div class="container">
    <div class="row">
        <div class="col-md-4" id="result">
            <?php
            if (!empty($_GET['page'])) {
                echo '<H2>Film Info:</H2>';
                $indexController = new FilmController(new Connection());
                $data = $indexController->getDataById($_GET['page']);
                foreach ($data as $key => $value) {
                    echo $key . ': ' . $value . '</br>';
                }
            } else {
                echo 'No Data to view';
            }
            ?>
        </div>
    </div>
</div>
</div>
</body>
</html>
