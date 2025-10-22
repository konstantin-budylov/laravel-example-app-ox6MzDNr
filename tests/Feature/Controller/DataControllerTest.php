<?php

namespace Tests\Feature\Controller;

use App\Import\Domain\Models\ImportedData;
use Tests\TestCase;

class DataControllerTest extends TestCase
{
    public function test_data_route_returns_json()
    {
        $response = $this->getJson(route('data'));

        $response->assertStatus(200);

        // Проверяем, что Content-Type содержит application/json
        $this->assertStringContainsString(
            'application/json',
            $response->headers->get('content-type') ?? ''
        );

        // Проверяем, что тело ответа — валидный JSON
        $this->assertJson($response->getContent());
    }

    public function test_index_returns_all_data_grouped_by_date()
    {
        // Подготовка данных
        ImportedData::create(['id' => 1, 'name' => 'A', 'date' => '2020-01-01']);
        ImportedData::create(['id' => 2, 'name' => 'B', 'date' => '2020-01-01']);
        ImportedData::create(['id' => 3, 'name' => 'C', 'date' => '2020-01-02']);

        $expected = [
            '2020-01-01' => [
                ['id' => 1, 'name' => 'A'],
                ['id' => 2, 'name' => 'B'],
            ],
            '2020-01-02' => [
                ['id' => 3, 'name' => 'C'],
            ],
        ];

        $response = $this->getJson(route('data'));

        $response->assertStatus(200);
        $response->assertExactJson($expected);
    }
}
