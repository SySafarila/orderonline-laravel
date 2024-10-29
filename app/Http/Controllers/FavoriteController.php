<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->pokemon_name) {
            $pokemon_name = $request->pokemon_name;
            $favorite = Favorite::orderBy('pokemon_name', 'asc')->where('pokemon_name', 'like', "%$pokemon_name%")->get();
        } else {
            $favorite = Favorite::orderBy('pokemon_name', 'asc')->get();
        }

        return response()->json($favorite);
    }

    public function isFavorite($name)
    {
        $find = Favorite::where('pokemon_name', $name)->first();
        if ($find) {
            return response()->json(['message' => $name . " is in favorite", 'status' => true]);
        }
        return response()->json(['message' => $name . " is not in favorite", 'status' => false]);
    }
}
