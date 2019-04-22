<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\TwitterUser;
use App\AutomaticTweet;

class AutomaticTweetApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->seed('UsersTableSeeder');
        $this->seed('TwitterUsersTableSeeder');
    }

    /**
     * @test
     */
    public function should_新しい自動ツイートを登録して返却()
    {
        $data = [
            'status' => 1,
            'tweet' => 'はじめてのツイート！',
            'date' => '2019-7-1',
            'time' => "21:20"
        ];
        $response = $this->actingAs($this->user)
            ->json('POST', route('tweet.add', $data));


        $response->assertStatus(201);
        $this->assertEquals($data['tweet'], AutomaticTweet::first()->tweet);
    }
}
