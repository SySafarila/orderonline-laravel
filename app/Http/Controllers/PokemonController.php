<?php

namespace App\Http\Controllers;

use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        try {
            $cacheKey = 'pokemon_' . $request->offset . '_' . $request->limit;

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

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

                Cache::put($cacheKey, $response, now()->addHours(6));

                return response()->json($response);
            }

            return response()->json(['message' => '3rd party API error.'], $response->status());
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), $th->getCode());
        }
    }

    public function show(string $id)
    {
        try {
            $cacheKey = 'show_pokemon_' . $id;

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::get("https://pokeapi.co/api/v2/pokemon/$id");
            if ($response->status() == 200) {
                $response = $response->object();
                Cache::put($cacheKey, ['abilities' => $response->abilities, 'species' => $response->species, 'sprites' => $response->sprites, 'height' => $response->height, 'weight' => $response->weight, 'id' => $response->id, 'types' => $response->types, 'name' => $response->name, 'status' => 200], now()->addHours(6));

                return ['abilities' => $response->abilities, 'species' => $response->species, 'sprites' => $response->sprites, 'height' => $response->height, 'weight' => $response->weight, 'id' => $response->id, 'types' => $response->types, 'name' => $response->name];
            }

            if ($response->status() == 404) {
                Cache::put($cacheKey, ['status' => 404], now()->addHours(3));
                return response()->json(['message' => 'Invalid pokemon'], 404);
            }

            return response()->json(['message' => '3rd party API error.'], $response->status());
        } catch (\Throwable $th) {
            // throw $th;
            return response($th->getMessage(), $th->getCode());
        }
    }
}
