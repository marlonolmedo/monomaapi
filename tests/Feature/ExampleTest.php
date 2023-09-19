<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_cache_connection(){
        Cache::set("prueba","hola mundo");

        $data = Cache::get('prueba');

        $this->assertSame("hola mundo", $data);
    }
}
