<?php

namespace repositories\contracts;

interface FilmInterface
{
    const BLU_RAY_FORMAT = 'Blu-Ray';
    const DVD_FORMAT = 'DVD';
    const VHS_FORMAT = 'VHS';

    const FILM_FORMATS = [
        self::BLU_RAY_FORMAT,
        self::DVD_FORMAT,
        self::VHS_FORMAT
    ];

    const ID_COLUMN = 'id';
    const FILM_NAME_COLUMN = 'film_name';
    const YEAR_COLUMN = 'year';
    const FORMAT_COLUMN = 'format';
    const ACTORS_COLUMN = 'actors';

    const FILMS_TABLE_COLUMNS = [
        self::ID_COLUMN => 'ID',
        self::FILM_NAME_COLUMN => 'Film name',
        self::YEAR_COLUMN => 'Year',
        self::FORMAT_COLUMN => 'Format'
    ];

    public function getFilmFormats(): array;

    public function all();

    public function create(array $data): void;

    public function delete(int $id): void;

    public function getFilmById(int $id);

    public function sortByColumn(string $column, $sortRule);

    public function searchByColumn(string $column, string $keyword);

    public function upload(array $dataToInsert):string;
}
