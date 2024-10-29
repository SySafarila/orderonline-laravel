<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $response = Http::get('https://pokeapi.co/api/v2/pokemon', [
                'offset' => $request->offset,
                'limit' => $request->limit
            ]);

            if ($response->status() == 200) {
                $response = $response->object();

                if ($response->next) {
                    $response->next = Str::replace('https://pokeapi.co/api/v2/pokemon', route('pokemons.index'), $response->next);
                }
                if ($response->previous) {
                    $response->previous = Str::replace('https://pokeapi.co/api/v2/pokemon', route('pokemons.index'), $response->previous);
                }

                foreach ($response->results as $pokemon) {
                    $pokemon->url = Str::replace("https://pokeapi.co/api/v2/pokemon", route('pokemons.index'), $pokemon->url);
                }
                return $response;
            }
            throw new Error($response, $response->status());
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * add to favorite
     */
    public function store(Request $request)
    {
        $request->validate([
            'pokemon_name' => ['required', 'string', 'max:255']
        ]);

        $find = Favorite::where('pokemon_name', $request->pokemon_name)->first();
        if ($find) {
            return response()->json(['message' => $request->pokemon_name . ' already in favorite'], 400);
        }

        Favorite::create([
            'pokemon_name' => $request->pokemon_name
        ]);

        return response()->json(['message' => $request->pokemon_name . ' added to favorite']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/$id");
            if ($response->status() == 200) {
                return $response->object();
            }
            throw new Error($response, $response->status());
        } catch (\Throwable $th) {
            // throw $th;
            return response($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * delete from favorite
     */
    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'pokemon_name' => ['required', 'string', 'max:255']
        ]);

        $find = Favorite::where('pokemon_name', $request->pokemon_name)->first();
        if ($find) {
            $find->delete();
            return response()->json(['message' => $request->pokemon_name . ' deleted from favorite']);
        }

        return response()->json(['message' => $request->pokemon_name . ' not in favorite', 400]);
    }
}
