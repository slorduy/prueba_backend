<?php

namespace Tests\Unit;

use Tests\TestCase;

class userTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testRegister_user_with_already_used_email()
    {
        $test_user = [
            'name' => 'juan jose',
            'email' => 'email@email.com',
            'cellphone' => 'email@email.com',
            'admin' => true,
        ];
        $response = $this->json('POST','/api/register',$test_user);
        $response->assertStatus(400);
    }


    public function testLogin_user_with_correct_credentials()
    {
        $test_user = [
            'email' => 'email@email.com',
            'password' => 12345678
        ];
        $response = $this->json('POST','/api/login',$test_user);
        
        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Acceso exitoso');

    }


}
