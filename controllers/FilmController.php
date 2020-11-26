<?php

namespace controllers;

include_once 'repositories/FilmRepository.php';

use database\Connection;
use repositories\FilmRepository;
use PDO;

/**
 * Class filmController
 * @package controllers
 */
class filmController
{
    /**
     * @var PDO
     */
    private $db;
    /**
     * @var
     */
    private $connection;

    /**
     * filmController constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $db->setDatabaseName($db::DB_WEBBYLABFILMS);
        $this->db = $db->make();
        $this->setConnection();
    }

    /**
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $errors = [];
        if (!isset($_POST['filmName']) || empty($_POST['filmName'])) {
            $errors['filmName'] = 'Film Name is required!';
        }
        if (!in_array($_POST['format'], $this->connection->getFilmFormats())) {
            $errors['filmName'] = 'Incorrect Film Format!';
        }
        if ($_POST['year'] < 1895) {
            $errors['year'] = 'Choose correct film release date!';
        }
        if (empty($_POST['actors'])) {
            $errors['actors'] = 'Actors are required!';
        };

        if (count($errors)) {
            return $errors;
        }
        $this->connection->create($data);
        return $errors;
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->connection->delete($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getDataById($id)
    {
        return $this->connection->getFilmById($id);
    }

    /**
     * @param string $column
     * @param string $sortRule
     * @return mixed
     */
    public function sortByColumn(string $column, $sortRule = 'desc')
    {
        return $this->connection->sortByColumn($column, $sortRule);
    }

    /**
     *
     */
    public function setConnection()
    {
        $this->connection = new FilmRepository($this->db);
    }

    /**
     * @param null $records
     * @return string
     */
    public function generateTable($records = null)
    {
        $tableFirstPart = '<tr>
                        <th>Id</th>
                        <th>Film name</th>
                        <th>Year</th>
                        <th>Format</th>
                        <th>Actors</th>
                        <th>Page</th>
                        <th>Delete</th>
                    </tr>';
        $tableMiddlePart = '';
        foreach ($records ?? $this->connection->all() as $data) {
            $id = $data['id'];
            $link = '<a href=' . "FilmPage.php?page=$id" . '>See More</a>';
            $tableMiddlePart .= '<tr><td>' . $id . '<td>' . $data['film_name'] . '</td>' . '<td>' . $data['year'] . '</td>'
                . '<td>' . $data['format'] . '</td>' . '<td>' . $data['actors'] . '</td>'
                . '<td><button id=filmPage' . ' name=' . $id . ">$link</button></td>"
                . '<td><form action="" id="deleteFilm" method="post"><button class="center" type="submit" value=' . $id . ' name=deleteFilm>delete</button></form></td></tr>';
        }
        return $tableFirstPart . $tableMiddlePart;
    }


    /**
     * @param string $column
     * @param string $keyword
     * @return mixed
     */
    public function searchByColumn(string $column, string $keyword = '')
    {
        return $this->connection->searchByColumn($column, $keyword);
    }

    /**
     * @param $fileName
     * @return string
     */
    public function upload($fileName)
    {
        $text = $this->readDocument($fileName);
        if (!$text) {
            return 'Incorrect file!';
        }
        $preparedText = preg_split('/ *(Title|Release Year|Format|Stars): /', $text);

        if (!strlen(trim($preparedText[0]))) {
            unset($preparedText[0]);
        }
        $dataToInsert = array_chunk($preparedText, 4);
        return $this->connection->upload($dataToInsert);
    }

    /**
     * @param $fileName
     * @return bool|string
     */
    public function readDocument($fileName)
    {
        $content = '';

        if (!$fileName || !file_exists($fileName)) return false;

        $zip = zip_open($fileName);
        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }
}
