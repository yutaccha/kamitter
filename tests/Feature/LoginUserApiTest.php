<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginUserApiTest extends TestCase
{

    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();

        // テストユーザー作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_登録済みのユーザーとしてログインして返却する()
    {
        $response = $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

        $this->assertAuthenticatedAs($this->user);
    }
}
