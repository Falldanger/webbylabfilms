<?php

namespace views;

use database\Connection;
use controllers\filmController;
use repositories\FilmRepository;

$filmFormats = FilmRepository::FILM_FORMATS;
$filmsTableColumnsForSorting = FilmRepository::FILMS_TABLE_COLUMNS;
$filmsTableColumnsForSearching = $filmsTableColumnsForSorting + [FilmRepository::ACTORS_COLUMN => 'Actors'];
$errors = [];

if (isset($_POST['addFilm'])) {
    $indexController = new filmController(new Connection());
    $errors = $indexController->create($_POST);
}
if (isset($_POST['deleteFilm'])) {
    $indexController = new filmController(new Connection());
    $indexController->delete($_POST['deleteFilm']);
}

if (isset($_POST['uploadFilms'])) {
    $indexController = new filmController(new Connection());
    $uploadStatus = $indexController->upload($_FILES['filmsFile']['tmp_name']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css"/>
    <link href="../css/style.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="#"/>
</head>
<body>
<div class="container">

    <h1>WebbyLab Films</h1>
    <div class="row">
        <div class="col-md-4">
            <form enctype="multipart/form-data" action="" id="uploadFilms" method="post">
                <div class="form-group">
                    <h5>Upload File</h5>
                    <input name="filmsFile" id="filmsFile" type="file"/>
                    <input type="submit" class="btn btn-primary" id="uploadFilms" name="uploadFilms" value="Submit"/>
                </div>
                <span><?php if (isset($uploadStatus)) {
                        echo $uploadStatus;
                    } ?></span>
            </form>
            <form action="" id="addFilm" method="post">
                <div class="form-group">
                    <label for="filmName">Film name</label>
                    <input type="text" class="form-control" id="filmName" name="filmName"
                           value="<?php if (isset($_POST['filmName'])) {
                               echo $_POST['filmName'];
                           } ?>">
                    <span>
                        <?php if (isset($errors['filmName'])) {
                            echo $errors['filmName'];
                        } ?>
                    </span>
                </div>
                <div class="form-group">
                    <label for="format">Format</label>
                    <select class="form-control" id="format" name="format" form="addFilm">
                        <?php
                        for ($i = 0; $i < count($filmFormats); $i++) {
                            echo '<option value=' . $filmFormats[$i] . ">$filmFormats[$i]</option>";
                        }
                        ?>
                    </select>
                    <span>
                        <?php if (isset($errors['format'])) {
                            echo $errors['format'];
                        } ?>
                    </span>
                </div>
                <div class="form-group">
                    <label for="year">Date Release</label>
                    <input type="number" id="year" name="year" value="<?php if (isset($_POST['year'])) {
                        echo $_POST['year'];
                    } ?>">
                    <span>
                        <?php if (isset($errors['year'])) {
                            echo $errors['year'];
                        } ?>
                    </span>
                </div>
                <div class="form-group">
                    <label for="actors">Actors</label>
                    <input type="text" class="form-control" id="actors" name="actors"
                           value="<?php if (isset($_POST['actors'])) {
                               echo $_POST['actors'];
                           } ?>">
                    <span>
                        <?php if (isset($errors['actors'])) {
                            echo $errors['actors'];
                        } ?>
                    </span>
                </div>
                <input type="submit" class="btn btn-primary" name="addFilm" value="Add Film">
            </form>
        </div>
        <div class="col-md-8">
            <form action="" id="sortFilms" method="post">
                <div class="row">
                    <div class="col-md-4">
                        <label for="sortByColumn">Column</label>
                        <select class="form-control" id="sortByColumn" name="sortByColumn" form="sortFilms">
                            <?php
                            foreach ($filmsTableColumnsForSorting as $key => $value) {
                                echo '<option value=' . $key . ">$value</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sortRule">Rule</label>
                        <select class="form-control" id="sortRule" name="sortRule" form="sortFilms">
                            <option value="desc">Desc</option>
                            <option value="asc">Asc</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="submit" class="btn btn-success sortFilms" name="sortFilms" id="sortFilms" value="Sort">
                    </div>
                </div>
            </form>
            <form action="" id="findFilms" method="post">
                <div class="row">
                    <div class="col-md-4">
                        <label for="findByColumn">Column</label>
                        <select class="form-control" id="findByColumn" name="findByColumn" form="findFilms">
                            <?php
                            foreach ($filmsTableColumnsForSearching as $key => $value) {
                                echo '<option value=' . $key . ">$value</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="findByKeyWord">Key Word</label>
                        <input type="text" class="form-control" id="findByKeyWord" name="findByKeyWord"
                               placeholder="type something">
                    </div>

                    <div class="col-md-4">
                        <input type="submit" class="btn btn-success findFilms" name="findFilms" id="findFilms" value="Search">
                    </div>
                </div>
            </form>
            <div>
                <table id="result">
                    <?php

                    $connection = new filmController(new Connection());
                    if (isset($_POST['sortFilms'])) {
                        echo $connection->generateTable($connection->sortByColumn($_POST['sortByColumn'], $_POST['sortRule']));
                    } elseif (isset($_POST['findFilms'])) {
                        echo $connection->generateTable($connection->searchByColumn($_POST['findByColumn'], $_POST['findByKeyWord']));
                    } else {
                        echo $connection->generateTable();
                    }

                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#year').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy'
    });
</script>
</body>
</html>
