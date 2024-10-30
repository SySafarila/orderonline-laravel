<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        $cacheKey = 'show_pokemon_for_favorite_' . $request->pokemon_name;

        if (Cache::has($cacheKey)) {
            $response_3rd_api = Cache::get($cacheKey);
            $response_3rd_api = json_decode(json_encode($response_3rd_api));
            if ($response_3rd_api->status == 404) {
                return response()->json(['message' => 'Invalid pokemon'], 404);
            }
        } else {
            $response_3rd_api = Http::get("https://pokeapi.co/api/v2/pokemon/" . $request->pokemon_name);

            if ($response_3rd_api->status() === 404) {
                Cache::put($cacheKey, ['status' => 404], now()->addHours(3));
                return response()->json(['message' => 'Invalid pokemon'], 404);
            }

            if ($response_3rd_api->status() !== 200) {
                return response()->json(['message' => '3rd party API error.'], $response_3rd_api->status());
            }

            $response_3rd_api = $response_3rd_api->object();
            Cache::put($cacheKey, ['abilities' => $response_3rd_api->abilities, 'species' => $response_3rd_api->species, 'sprites' => $response_3rd_api->sprites, 'height' => $response_3rd_api->height, 'weight' => $response_3rd_api->weight, 'id' => $response_3rd_api->id, 'types' => $response_3rd_api->types, 'name' => $response_3rd_api->name, 'status' => 200], now()->addHours(6));
        }

        $find = Favorite::where('pokemon_name', $request->pokemon_name)->first();
        if ($find) {
            return response()->json(['message' => $request->pokemon_name . ' already in favorite'], 400);
        }

        $abilities = [];
        if ($response_3rd_api->abilities) {
            foreach ($response_3rd_api->abilities as $ability) {
                array_push($abilities, [
                    'name' => $ability->ability->name,
                    'is_hidden' => $ability->is_hidden
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

        return response()->json(['message' => $name . ' not in favorite'], 400);
    }
}
