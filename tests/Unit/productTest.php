<?php

namespace Tests\Unit;

use Tests\TestCase;

class productTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testProduct_with_invalid_price_with_admin_user()
    {
        
        $test_product = [
            'name' => 'Pizza',
            'description' => 'pizza personal',
            'price' => 'precio invalido'
        ];
        $response = $this->json('POST','/api/product/create?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MTc3MjI4NywiZXhwIjoxNjQxNzc1ODg3LCJuYmYiOjE2NDE3NzIyODcsImp0aSI6Im1RcUFUSnJ2TDl1bHZ1b0EiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Y2L6Xk0isdbCdQoDiv4p5_0QDOob_Bqez7S6Oo79yKQ',$test_product);
        
        $response
        ->assertStatus(400)
        ->assertJsonPath('message', 'El precio no es valido');
    }

    public function testUpdate_product_not_existing()
    {
        
        $test_product = [
            'name' => 'Pizza',
            'description' => 'pizza personal',
            'price' => 5000,
            'id' => 111111
        ];
        $response = $this->json('POST','/api/product/update?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTY0MTc3MjI4NywiZXhwIjoxNjQxNzc1ODg3LCJuYmYiOjE2NDE3NzIyODcsImp0aSI6Im1RcUFUSnJ2TDl1bHZ1b0EiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Y2L6Xk0isdbCdQoDiv4p5_0QDOob_Bqez7S6Oo79yKQ',$test_product);
        
        $response
        ->assertStatus(400)
        ->assertJsonPath('message', 'Producto no encontrado');
    }
}
