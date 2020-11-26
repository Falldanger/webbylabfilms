<?php

include_once 'database\Connection.php';
include_once 'controllers\FilmController.php';

use database\Connection;
use controllers\filmController;

$manipulations = new filmController(new Connection());
$filmFormats = ['VHS', 'DVD', 'Blu-Ray'];

if (isset($_POST['addFilm'])) {
    $errors = [];
    if (!isset($_POST['filmName']) || empty($_POST['filmName'])) {
        $errors['filmName'] = 'Film Name is required!';
    }
    if (!in_array($_POST['format'], $filmFormats)) {
        $errors['filmName'] = 'Incorrect Film Format!';
    }
    if (!is_int($_POST['year']) || ($_POST['year'] < 1895)) {
        $errors['year'] = 'Choose correct film release date!';
    }
    if (empty($_POST['actors'])) {
        $errors['actors'] = 'Actors are required!';
    }
    if (count($errors)) {
        return $errors;
    }

    //addfunction
}
if (isset($_POST['delete']) && $_POST['delete'] == 1) {
    if ($manipulations->delete($_POST['id'])) {
        $test = $manipulations->index();
        echo json_encode($test);
    }
}
