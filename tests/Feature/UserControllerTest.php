<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
     /**
     * Successfull Registration
     * This test is to check user Registered Successfully or not
     * @test
     */
    public function test_successfulRegistration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "balaji",
                "lastname" => "kumar",
                "email" => "balajishkumar@gmail.com",
                "password" => "balaji@123",
                "password_confirmation" => "balaji@123"
            ]);
        $response->assertStatus(201)->assertJson(['message' => 'User successfully registered']);
    }

    /**
     * Test to check the user is already registered
     * by using first_name, last_name, email and password as credentials
     * The email used is a registered email for this test
     * 
     * @test
     */


    public function test_userisAlreadyRegistered()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "ramesh",
                "lastname" => "kumar",
                "email" => "rameshkumar@gmail.com",
                "password" => "ramesh@123",
                "password_confirmation" => "ramesh@123"
            ]);
        $response->assertStatus(401)->assertJson(['message' => 'The email has already been taken.']);
    }

    /**
     * Test for successful Login
     * Login the user by using the email and password as credentials
     * 
     * @test
     */
    public function test_successfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/login',
                [
                    "email" => "rameshkumar@gmail.com",
                    "password" => "ramesh@123"
                ]
            );
        $response->assertStatus(200)->assertJson(['message' => 'Login successful']);
    }

     /**
     * Test for Unsuccessfull Login
     * Login the user by email and password
     * Wrong password for this test
     * 
     * @test
     */

    public function test_UnSuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json(
                'POST',
                '/api/login',
                [
                    "email" => "rameshkumar@gmail.com",
                    "password" => "ramesh@23"
                ]
            );
        $response->assertStatus(402)->assertJson(['message' => 'Wrong Password']);
    }

     /**
     * Test for Successfull Forgot Password
     * Send a mail for forgot password of a registered user
     * 
     * @test
     */

    public function test_SuccessfulForgotPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/forgotPassword', [
                "email" => "tarunkumar@gmail.com"
            ]);

        $response->assertStatus(201)->assertJson(['message' => 'Reset link Sent to your Email']);
    }

     /**
     * Test for UnSuccessfull Forgot Password
     * Send a mail for forgot password of a registered user
     * Non-Registered email for this test
     * 
     * @test
     */

    public function test_unsuccessfulForgotPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/forgotPassword', [
                "email" => "api@gmail.com"
            ]);

        $response->assertStatus(402)->assertJson(['message' => 'Email is not registered']);
    }

     /**
     * Test for Successfull Reset Password
     * Reset password using the token and 
     * setting the new password to be the password
     * 
     * @test
     */

    public function test_successfulResetPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/resetPassword', [
                "new_password" => "prem@335",
                "password_confirmation" => "prem@335",
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2ZvcmdvdFBhc3N3b3JkIiwiaWF0IjoxNjU3ODYwMTAxLCJleHAiOjE2NTc4NjM3MDEsIm5iZiI6MTY1Nzg2MDEwMSwianRpIjoiZ3lDT2FxZ3QxYW9sNG9SVSIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.jJ-O-262Hz9V6SlFH9gqYhBF6pwIrlbVcy8iu5D5qvU'
            ]);

        $response->assertStatus(200)->assertJson(['message' => 'Password reset successfull!']);
    }

    
}
