<?php

namespace Tests\Feature;

use App\User;
use function factory;
use function route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutUserApiTest extends TestCase
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
    public function should_認証済みのユーザーをログアウト() {
        $response = $this->actingAs($this->user)
            ->json('POST', route('logout'));

        $response->assertStatus(200);
        $this->assertGuest();
    }
}
