<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MovieService
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

    public function getAllCategories()
    {
        return Category::all();
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

        $randomName = Str::uuid()->toString();
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = $randomName . '.' . $fileExtension;

        // Simpan file foto ke folder public/images
        $file->move(public_path('images'), $fileName);

        // Simpan data ke table movies
        return Movie::create([
            'id' => $data['id'],
            'judul' => $data['judul'],
            'category_id' => $data['category_id'],
            'sinopsis' => $data['sinopsis'],
            'tahun' => $data['tahun'],
            'pemain' => $data['pemain'],
            'foto_sampul' => $fileName,
        ]);
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

        $movie = Movie::findOrFail($id);

        $updateData = collect($data)->except('foto_sampul')->toArray();

        if ($file) {
            $randomName = Str::uuid()->toString();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = $randomName . '.' . $fileExtension;

            // Simpan file foto ke folder public/images
            $file->move(public_path('images'), $fileName);

            // Hapus foto lama jika ada
            $this->deleteFile($movie->foto_sampul);

            $updateData['foto_sampul'] = $fileName;
        }

        $movie->update($updateData);
        return $movie;
    }

    public function deleteMovie($id)
    {
        $movie = Movie::findOrFail($id);

        // Delete the movie's photo if it exists
        $this->deleteFile($movie->foto_sampul);

        // Delete the movie record from the database
        return $movie->delete();
    }

    private function deleteFile($fileName)
    {
        if ($fileName && File::exists(public_path('images/' . $fileName))) {
            File::delete(public_path('images/' . $fileName));
        }
    }
}
