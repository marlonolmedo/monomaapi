<?php

namespace Tests\Unit;

use App\Utilities\Utility;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_message_maker_success_one_array()
    {
        $data = [
            "id" => 1,
            "name" => "Role 1",
            "created_at" => "today",
            "created" => "today"
        ];
        $message = Utility::message(true, data: $data);

        $expected = [
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => $data
        ];

        $this->assertEquals($expected, $message);
    }

    public function test_message_maker_success_many_items()
    {
        $data = [
            [
                "id" => 1,
                "name" => "Role 1",
                "created_at" => "today",
                "created" => "today"
            ],
            [
                "id" => 2,
                "name" => "Role 2",
                "created_at" => "today",
                "created" => "today"
            ]
        ];
        $message = Utility::message(true, data: $data);

        $expected = [
            "meta" => [
                "success" => true,
                "errors" => []
            ],
            "data" => $data
        ];

        $this->assertArrayHasKey("data", $message);
        $this->assertEquals($expected, $message);
    }

    public function test_message_maker_failed(){
        $error = [
            "Not Found."
        ];
        $message = Utility::message(false, $error);
        $expected = [
            "meta" => [
                "success" => false,
                "errors" => ["Not Found."]
            ]
        ];

        $this->assertArrayNotHasKey("data",$message);
        $this->assertEquals($expected,$message);
    }
}
