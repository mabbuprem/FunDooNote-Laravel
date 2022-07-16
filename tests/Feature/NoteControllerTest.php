<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
   
    protected static $token;
    public static function setUpBeforeClass(): void
    {
        self::$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU3ODk2MjkxLCJleHAiOjE2NTc4OTk4OTEsIm5iZiI6MTY1Nzg5NjI5MSwianRpIjoiSFc1ZHppbnpaNGs5VldEayIsInN1YiI6IjEwIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.iTZFeWfnNzQ7L3zxGiJWwbrUZ7yoKkAfv0buoYRNJvg";
    }

    /**
     * Successful Create Note Test
     * Using Credentials Required and
     * using the authorization token
     * 
     * @test
     */
    public function successfulCreateNoteTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/createNote', [
                "title" => "workas",
                "description" => "Do the Work",
                "token" => self::$token
            ]);
        $response->assertStatus(200)->assertJson(['message' => 'Note created successfully']);
    }

    /**
     * UnSuccessful Create Note Test
     * Using Credentials Required and
     * using the authorization token
     * Wrong Credentials is used for this test
     * 
     * @test
     */
    public function unSuccessfulCreateNoteTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/createnote', [
                "title" => "Work",
                "description" => "Do the Work",
                "token" => self::$token
            ]);
        $response->assertStatus(400)->assertJson(['message' => 'title should be unique']);
    }

    /**
     * Successful Update Note By ID Test
     * Update a note using id and authorization token
     * 
     * @test
     */
    public function successfulUpdateNoteByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/updatenotebyid', [
                "id" => "4",
                "title" => "samsung",
                "token" => self::$token
            ]);
        $response->assertStatus(201)->assertJson(['message' => 'Note Updated Successfully']);
    }

    /**
     * UnSuccessful Update Note By ID Test
     * Update a note using id and authorization token
     * Passing wrong note or noteId which is not for this user, for this test
     * 
     * @test
     */
    public function unSuccessfulUpdateNoteByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/updatenotebyid', [
                "id" => "2",
                "title" => "Expence",
                "description" => "Write Down Your Expences",
                "token" => self::$token
            ]);
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }

    /**
     * Successful Delete Note By ID Test
     * Delete note by using id and authorization token
     * 
     * @test
     */
    public function successfulDeleteNoteByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/deletenotebyid', [
                "id" => "4",
                "token" => self::$token
            ]);
        $response->assertStatus(200)->assertJson(['message' => 'Note Deleted Successfully']);
    }

    /**
     * UnSuccessful Delete Note By ID Test
     * Delete note by using id and authorization token
     * Passing wrong note or noteId which is not for this user, for this test
     * 
     * @test
     */
    public function unSuccessfulDeleteNoteByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/deletenotebyid', [
                "id" => "80",
                "token" => self::$token
            ]);
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }

}
