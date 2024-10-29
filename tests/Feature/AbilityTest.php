<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbilityTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_get_abilities(): void
    {
        $response = $this->get(route('abilities.index'));
        $response->assertStatus(200);
    }

    public function test_get_pokemons_by_ability(): void
    {
        $response = $this->get(route('abilities.show', ['ability' => 'stench']));
        $response->assertStatus(200);
    }
}
