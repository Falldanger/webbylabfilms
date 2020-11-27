<?php

namespace repositories;

include_once './repositories/contracts/FilmInterface.php';

use PDOException;
use repositories\contracts\FilmInterface;
use PDO;

/**
 * Class FilmRepository
 * @package repositories
 */
class FilmRepository implements FilmInterface
{
    const FILMS_TABLE = 'films';
    /**
     * @var PDO
     */
    private $db;

    /**
     * FilmRepository constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return array
     */
    public function getFilmFormats(): array
    {
        return self::FILM_FORMATS;
    }

    /**
     * @return array
     */
    public function all()
    {
        $query = $this->db->query("SELECT * FROM " . self::FILMS_TABLE . " ORDER BY ID DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param array $data
     */
    public function create(array $data): void
    {
        $query = "INSERT INTO " . self::FILMS_TABLE . "(`film_name`, `format`,`year`,`actors`)
        VALUES ('{$data['filmName']}', '{$data['format']}', '{$data['year']}', '{$data['actors']}')";
        $this->db->prepare($query)->execute();
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $query = "DELETE FROM " . self::FILMS_TABLE . " WHERE id=" . $id;
        $this->db->prepare($query)->execute();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getFilmById(int $id)
    {
        $query = $this->db->prepare("SELECT * FROM " . self::FILMS_TABLE . " WHERE id=:id");
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $column
     * @param string $sortRule
     * @return array
     */
    public function sortByColumn(string $column, $sortRule = 'desc')
    {
        $query = $this->db->prepare("SELECT * from " . self::FILMS_TABLE . " ORDER BY $column $sortRule");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $column
     * @param string $keyword
     * @return array
     */
    public function searchByColumn(string $column, string $keyword = '')
    {
        $keyword = '\'%' . $keyword . '%\'';
        $query = $this->db->query("SELECT * FROM " . self::FILMS_TABLE . " WHERE " . $column . " LIKE " . $keyword);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param array $dataToInsert
     * @return string
     */
    public function upload(array $dataToInsert = []): string
    {
        $colNames = [self::FILM_NAME_COLUMN, self::YEAR_COLUMN, self::FORMAT_COLUMN, self::ACTORS_COLUMN];
        if (!isset($dataToInsert[0])) {
            return 'No Data to insert';
        }
        $question_marks[] = '(' . $this->placeholders('?', count($dataToInsert[0])) . ')';

        $sql = "INSERT INTO " . self::FILMS_TABLE . "(" . implode(",", $colNames) . ") VALUES " . implode(',', $question_marks);

        $stmt = $this->db->prepare($sql);
        try {
            foreach ($dataToInsert as $record) {
                $stmt->execute($record);
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
        return "Data upload successfully";
    }

    /**
     * @param $text
     * @param int $count
     * @param string $separator
     * @return string
     */
    public function placeholders($text, $count = 0, $separator = ",")
    {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }
}
