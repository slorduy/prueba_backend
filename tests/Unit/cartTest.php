<?php

namespace Tests\Unit;

use Tests\TestCase;

class cartTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAdd_car_with_not_existing_user_id()
    {
        $test_cart = [
            'product_id' => 21,
            'quantity' => 1,
            'user_id' => 1111
        ];

        $response = $this->json('POST','/api/cart/add?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MTc3MjI4NywiZXhwIjoxNjQxNzc1ODg3LCJuYmYiOjE2NDE3NzIyODcsImp0aSI6Im1RcUFUSnJ2TDl1bHZ1b0EiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Y2L6Xk0isdbCdQoDiv4p5_0QDOob_Bqez7S6Oo79yKQ',$test_cart);
        
        $response
        ->assertStatus(400)
        ->assertJsonPath('message', 'El usuario no existe');
    }


    public function testAdd_car_succesfully()
    {
        $test_cart = [
            'product_id' => 21,
            'quantity' => 1,
            'user_id' => 1
        ];

        $response = $this->json('POST','/api/cart/add?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MTc3MjI4NywiZXhwIjoxNjQxNzc1ODg3LCJuYmYiOjE2NDE3NzIyODcsImp0aSI6Im1RcUFUSnJ2TDl1bHZ1b0EiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Y2L6Xk0isdbCdQoDiv4p5_0QDOob_Bqez7S6Oo79yKQ',$test_cart);
        
        $response
        ->assertStatus(200)
        ->assertJsonPath('status', false);
    }
}
