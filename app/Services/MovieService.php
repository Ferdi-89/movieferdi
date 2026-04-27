<?php

namespace App\Services;

use App\Interfaces\MovieRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MovieService
{
    protected $movieRepository;
    protected $categoryRepository;

    public function __construct(MovieRepositoryInterface $movieRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->movieRepository = $movieRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllMovies($search = null, $perPage = 6)
    {
        return $this->movieRepository->getAllMovies($search, $perPage);
    }

    public function getMovieById($id)
    {
        return $this->movieRepository->getMovieById($id);
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->getAllCategories();
    }

    public function storeMovie(array $data, $file)
    {
        Validator::make($data, [
            'id' => ['required', 'string', 'max:255', Rule::unique('movies', 'id')],
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
            'foto_sampul' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ])->validate();

        $fileName = $this->uploadImage($file);

        $movieData = [
            'id' => $data['id'],
            'judul' => $data['judul'],
            'category_id' => $data['category_id'],
            'sinopsis' => $data['sinopsis'],
            'tahun' => $data['tahun'],
            'pemain' => $data['pemain'],
            'foto_sampul' => $fileName,
        ];

        // Simpan data ke table movies
        return $this->movieRepository->createMovie($movieData);
    }

    public function updateMovie($id, array $data, $file = null)
    {
        Validator::make($data, [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
            'foto_sampul' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ])->validate();

        $movie = $this->movieRepository->getMovieById($id);

        $updateData = collect($data)->except('foto_sampul')->toArray();

        if ($file) {
            $fileName = $this->uploadImage($file);

            // Hapus foto lama jika ada
            $this->deleteFile($movie->foto_sampul);

            $updateData['foto_sampul'] = $fileName;
        }

        return $this->movieRepository->updateMovie($id, $updateData);
    }

    public function deleteMovie($id)
    {
        $movie = $this->movieRepository->getMovieById($id);

        // Delete the movie's photo if it exists
        $this->deleteFile($movie->foto_sampul);

        // Delete the movie record from the database
        return $this->movieRepository->deleteMovie($id);
    }

    private function deleteFile($fileName)
    {
        if ($fileName && File::exists(public_path('images/' . $fileName))) {
            File::delete(public_path('images/' . $fileName));
        }
    }

    private function uploadImage($file)
    {
        $fileName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $fileName);
        return $fileName;
    }
}
