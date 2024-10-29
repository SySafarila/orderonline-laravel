<?php

namespace App\Http\Controllers;

use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AbilityController extends Controller
{
    public function index(Request $request)
    {
        try {
            $cacheKey = 'ability_' . $request->offset . '_' . $request->limit;

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::get('https://pokeapi.co/api/v2/ability', [
                'offset' => $request->offset,
                'limit' => $request->limit
            ]);

            if ($response->status() == 200) {
                $response = $response->object();

                if ($response->next) {
                    $response->next = Str::replace('https://pokeapi.co/api/v2/ability', route('abilities.index'), $response->next);
                }
                if ($response->previous) {
                    $response->previous = Str::replace('https://pokeapi.co/api/v2/ability', route('abilities.index'), $response->previous);
                }

                foreach ($response->results as $ability) {
                    $ability->url = Str::replace("https://pokeapi.co/api/v2/ability", route('abilities.index'), $ability->url);
                }

                Cache::put($cacheKey, $response, now()->addHours(6));

                return response()->json($response);
            }
            throw new Error($response, $response->status());
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), $th->getCode());
        }
    }

    public function show(string $id)
    {
        try {
            $cacheKey = 'show_ability_' . $id;

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::get("https://pokeapi.co/api/v2/ability/$id");
            if ($response->status() == 200) {
                $response = $response->object();
                Cache::put($cacheKey, ['pokemons' => $response->pokemon, 'name' => $response->name, 'id' => $response->id], now()->addHours(6));

                return ['pokemons' => $response->pokemon, 'name' => $response->name, 'id' => $response->id];
            }
            throw new Error($response, $response->status());
        } catch (\Throwable $th) {
            // throw $th;
            return response($th->getMessage(), $th->getCode());
        }
    }
}
