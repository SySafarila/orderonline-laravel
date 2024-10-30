<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PokemonTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_get_pokemons_data(): void
    {
        $response = $this->get(route('pokemons.index'));
        $response->assertStatus(200);
    }

    public function test_get_pokemon_detail(): void
    {
        $responseSuccess = $this->get(route('pokemons.show', ['pokemon' => 'bulbasaur']));
        $responseSuccess->assertStatus(200);

        $responseFail = $this->get(route('pokemons.show', ['pokemon' => 'bulbasaur222']));
        $responseFail->assertStatus(404);
    }
}
