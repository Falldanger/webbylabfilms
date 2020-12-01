<?php

namespace controllers;

include_once './repositories/FilmRepository.php';
include_once './rules/BlackListSymbolsInterface.php';

use database\Connection;
use repositories\FilmRepository;
use rules\BlackListSymbolsInterface;
use PDO;


/**
 * Class filmController
 * @package controllers
 */
class filmController implements BlackListSymbolsInterface
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
        $data = str_replace(self::BLACK_LIST, "", $data);
        if (!isset($data['filmName']) || empty($data['filmName'])) {
            $errors['filmName'] = 'Film Name is required!';
        }
        if (!in_array($data['format'], $this->connection->getFilmFormats())) {
            $errors['filmName'] = 'Incorrect Film Format!';
        }
        if ($data['year'] < 1895) {
            $errors['year'] = 'Choose correct film release date!';
        }
        if (empty($data['actors'])) {
            $errors['actors'] = 'Actors are required!';
        };
        if (isset($data['actors'])) {
            $possibleActors = explode(',', $data['actors']);
            $preparedActors = [];
            for ($i = 0; $i < count($possibleActors); $i++) {
                $preparedActors[] = strtolower(trim($possibleActors[$i]));
            }
            if (count($preparedActors) != count(array_unique($preparedActors))) {
                $errors['actors'] = 'The same authors are present in line!';
            }
        }

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
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param array $records
     * @return string
     */
    public function generateTable($records = [])
    {
        if (empty($records)) {
            return 'Data not found';
        }
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
        foreach ($records as $data) {
            $id = $data['id'];
            $link = '<a href=' . "FilmPage.php?page=$id" . '>See More</a>';
            $tableMiddlePart .= '<tr><td>' . $id . '<td>' . $data['film_name'] . '</td>' . '<td>' . $data['year'] . '</td>'
                . '<td>' . $data['format'] . '</td>' . '<td>' . $data['actors'] . '</td>'
                . '<td><button id=filmPage' . ' name=' . $id . ">$link</button></td>"
                . '<td><form action="" id="deleteFilm" method="post"><button onclick="return confirm(\'Are you sure?\')" class="center" type="submit" value=' . $id . ' name=deleteFilm>delete</button></form></td></tr>';
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
     * @param $filePath
     * @param $fileName
     * @return string
     */
    public function upload($filePath, $fileName)
    {
        $content = $this->readDocument($filePath, $fileName);

        if (isset($content['error'])) {
            return $content['error'];
        }
        $preparedText = preg_split('/ *(Title|Release Year|Format|Stars): /', $content['text']);
        if (!isset($preparedText[1])) {
            return 'No Data to insert';
        }

        if (!strlen(trim($preparedText[0]))) {
            unset($preparedText[0]);
        }
        $dataToInsert = array_chunk($preparedText, 4);
        return $this->connection->upload($dataToInsert);
    }

    /**
     * @param $filePath
     * @param $fileName
     * @return array
     */
    public function readDocument($filePath, $fileName)
    {
        $fileExtension = $this->getFileExtension($fileName);
        switch ($fileExtension) {
            case '.txt':
                $content = ['text' => file_get_contents($filePath)];
                break;
            case 'docx':
                $content = $this->readDocxFile($filePath);
                break;
            default:
                $content = ['error' => 'Extension not supported'];
        }

        return $content;
    }

    /**
     * @param $fileName
     * @return bool|string
     */
    public function getFileExtension($fileName)
    {
        return substr($fileName, -4, 4);
    }

    /**
     * @param $fileName
     * @return array
     */
    public function readDocxFile($fileName)
    {
        $content = '';

        $zip = zip_open($fileName);
        if (!$zip || is_numeric($zip)) return ['error' => "Cannot open file!"];

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
        return ['text' => $striped_content];
    }
}
