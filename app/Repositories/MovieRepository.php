<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Interfaces\MovieRepositoryInterface;

class MovieRepository implements MovieRepositoryInterface
{
    public function getAllMovies($search = null, $perPage = 6)
    {
        $query = Movie::latest();
        if ($search) {
            $query->where('judul', 'like', '%' . $search . '%')
                ->orWhere('sinopsis', 'like', '%' . $search . '%');
        }
        return $query->paginate($perPage)->withQueryString();
    }

    public function getMovieById($id)
    {
        return Movie::findOrFail($id);
    }

    public function createMovie(array $data)
    {
        return Movie::create($data);
    }

    public function updateMovie($id, array $data)
    {
        $movie = $this->getMovieById($id);
        $movie->update($data);
        return $movie;
    }

    public function deleteMovie($id)
    {
        $movie = $this->getMovieById($id);
        return $movie->delete();
    }
}
