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
        $response = $this->get(route('pokemons.show', ['pokemon' => 'bulbasaur']));
        $response->assertStatus(200);
    }
}
