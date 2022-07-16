<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelControllerTest extends TestCase
{
    protected static $token;
    public static function setUpBeforeClass(): void
    {
        self::$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY1MDkwMjg3NywiZXhwIjoxNjUwOTA2NDc3LCJuYmYiOjE2NTA5MDI4NzcsImp0aSI6IkE5ZjJocTNRUWp5TTNKQ00iLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.ru6jr674g0ZQoSKUJsOfMqFiuoLKaJapV_dQPEURF3o";
    }

    /**
     * Successful Create Label Test
     * Create a label using label_name and authorization token for a user
     * 
     * @test
     */
    public function successfulCreateLabelTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/createLabel', [
                "labelname" => "Presentation",
                "token" => self::$token
            ]);
        $response->assertStatus(200)->assertJson(['message' => 'Lable successfully created']);
    }

    /**
     * UnSuccessful Create Label Test
     * Create a label using label_name and authorization token for a user
     * Using existing label name for this test
     * 
     * @test
     */
    public function unSuccessfulCreateLabelTest()
    {

        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/createlabel', [
                "labelname" => "Presentation",
                "token" => self::$token
            ]);
        $response->assertStatus(409)->assertJson(['message' => 'Label Name Already Exists']);
    }

    /**
     * Successful Update Label Test
     * Update label using label_id, label_name and authorization
     * 
     * @test
     */
    public function successfulupdateLabelByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/updateLabelById', [
                'id' => '14',
                'labelname' => 'Office Work',
                "token" => self::$token
            ]);
        $response->assertStatus(201)->assertJson(['message' => 'Label Updated Successfully']);
    }

    /**
     * UnSuccessful Update Label Test
     * Update label using label_id, label_name and authorization
     * Using existing label name for this test
     * 
     * @test
     */
    public function unSuccessfulupdateLabelByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/updateLabelById', [
                'id' => '14',
                'labelname' => 'Office Work',
                "token" => self::$token
            ]);
        $response->assertStatus(201)->assertJson(['message' => 'label updated successfully']);
    }

    /**
     * Successful Delete Label Test
     * Delete Label using label_id and authorization token
     * @test
     */
    public function successfuldeleteLabelById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/deleteLabelById', [
                "id" => "6",
                "token" => self::$token
            ]);
        $response->assertStatus(200)->assertJson(['message' => 'label deleted Successfully']);
    }

    /**
     * UnSuccessful Delete Label Test
     * Delete Label using label_id and authorization token
     * Giving wrong label_id for this test
     * 
     * @test
     */
    public function unSuccessfuldeleteLabelByIdTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/deleteLabelById', [
                "id" => "14",
                "token" => self::$token
            ]);
        $response->assertStatus(404)->assertJson(['message' => 'Label Not Found']);
    }
}

