<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
     /**
     * Successfull registration
     * This test is for to see if user is getting Register Successfully
     *
     * @test
     */
    public function TestsuccessfulRegistrationTest()
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
