<?php

namespace App\Http\Controllers;

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
     */
    public function store(Request $request)
    {
        //
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
     */
    public function destroy(string $id)
    {
        //
    }
}
