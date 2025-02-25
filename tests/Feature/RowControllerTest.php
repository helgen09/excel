<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Row;

class RowControllerTest extends TestCase
{
    public function test_index()
    {
        Row::factory()->create(['date' => '2023-10-01']);
        $response = $this->getJson('/api/rows');
        $response->assertStatus(200)->assertJsonStructure([['date', 'items']]);
    }
}