<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->pokemon_name) {
            $pokemon_name = $request->pokemon_name;
            $favorite = Favorite::with('abilities')->orderBy('pokemon_name', 'asc')->where('pokemon_name', 'like', "%$pokemon_name%")->get();
        } else {
            $favorite = Favorite::with('abilities')->orderBy('pokemon_name', 'asc')->get();
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

    public function store(Request $request)
    {
        $request->validate([
            'pokemon_name' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable'],
            'abilities.*.name' => ['required', 'string', 'max:255'],
            'abilities.*.is_hidden' => ['required', 'boolean']
        ]);

        $find = Favorite::where('pokemon_name', $request->pokemon_name)->first();
        if ($find) {
            return response()->json(['message' => $request->pokemon_name . ' already in favorite'], 400);
        }

        $abilities = [];
        if ($request->abilities) {
            foreach ($request->abilities as $ability) {
                array_push($abilities, [
                    'name' => $ability['name'],
                    'is_hidden' => $ability['is_hidden']
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $favorite = Favorite::create([
                'pokemon_name' => $request->pokemon_name
            ]);

            $favorite->abilities()->createMany($abilities);

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
        }

        return response()->json(['message' => $request->pokemon_name . ' added to favorite']);
    }

    public function destroy(string $name)
    {
        $find = Favorite::where('pokemon_name', $name)->first();
        if ($find) {
            $find->delete();
            return response()->json(['message' => $name . ' deleted from favorite']);
        }

        return response()->json(['message' => $name . ' not in favorite', 400]);
    }
}
