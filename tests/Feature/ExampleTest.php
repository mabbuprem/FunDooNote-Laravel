<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function successfulRegistrationTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "prem",
                "lastname" => "kumar",
                "email" => "mabbupremkumar@gmail.com",
                "password" => "Prem335@",
                "password_confirmation" => "Prem335@"
            ]);
        $response->assertStatus(201)->assertJson(['message' => 'User successfully registered']);
    }
}
