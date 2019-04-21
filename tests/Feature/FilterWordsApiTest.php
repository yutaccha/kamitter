<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\FilterWord;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FilterWordsApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

       $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_新規キーワードの登録して返却()
    {
        $data = [
            'type' => 1,
            'and' => 'ウェブカツ かみったー',
            'or' => '',
            'not' => '',
        ];
        $response = $this->actingAs($this->user)
            ->json('POST', route('filter.add', $data));

        $response->assertStatus(201);
        $this->assertEquals($data['and'], FilterWord::first()->and);

    }
}
