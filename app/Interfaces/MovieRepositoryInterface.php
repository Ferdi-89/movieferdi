<?php

namespace App\Interfaces;

interface MovieRepositoryInterface
{
    public function getAllMovies($search = null, $perPage = 6);
    public function getMovieById($id);
    public function createMovie(array $data);
    public function updateMovie($id, array $data);
    public function deleteMovie($id);
}
