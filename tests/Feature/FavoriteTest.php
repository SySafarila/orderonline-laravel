<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_get_favorite_pokemons(): void
    {
        $response = $this->get(route('favorite.index'));
        $response->assertStatus(200);
    }

    public function test_add_favorite_pokemon(): void
    {
        $responseSuccess = $this->post(route('favorite.store'), [
            'pokemon_name' => 'bulbasaur'
        ]);
        $responseSuccess->assertStatus(200);

        $responseFail = $this->post(route('favorite.store'), [
            'pokemon_name' => 'bulbasaurxxx'
        ]);
        $responseFail->assertStatus(404);
    }

    public function test_check_pokemon_favorite(): void
    {
        $responseAddFavorite = $this->post(route('favorite.store'), [
            'pokemon_name' => 'bulbasaur',
            'abilities' => [
                [
                    'name' => 'overgrow',
                    'is_hidden' => false
                ],
                [
                    'name' => 'chlorophyll',
                    'is_hidden' => true
                ]
            ]
        ]);
        $responseAddFavorite->assertStatus(200);

        $responseCheckFavorite = $this->get(route('favorite.check', ['name' => 'bulbasaur']));
        $responseCheckFavorite->assertStatus(200);
    }

    public function test_remove_favorite_pokemon(): void
    {
        $responseAddFavorite = $this->post(route('favorite.store'), [
            'pokemon_name' => 'bulbasaur',
            'abilities' => [
                [
                    'name' => 'overgrow',
                    'is_hidden' => false
                ],
                [
                    'name' => 'chlorophyll',
                    'is_hidden' => true
                ]
            ]
        ]);
        $responseAddFavorite->assertStatus(200);

        $responseCheckFavorite = $this->get(route('favorite.check', ['name' => 'bulbasaur']));
        $responseCheckFavorite->assertStatus(200);

        $responseRemoveFavorite = $this->delete(route('favorite.destroy', ['name' => 'bulbasaur']));
        $responseRemoveFavorite->assertStatus(200);
    }
}
