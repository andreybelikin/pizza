<?php

namespace Tests\Feature\Controller\Auth;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    private const USER_CONTROLLER_ROUTE = '/api/auth/register';

    public function testRegisterShouldSuccess(): void
    {
        $registerData = [
            'name' => 'andrey',
            'surname' => 'prostoandrey',
            'email' => 'test2233@email.com',
            'phone' => '79987871698',
            'password' => 'keK48!>O04780',
            'password_confirmation' => 'keK48!>O04780',
            'default_address' => 'г. Москва',
        ];
        $response = $this->postJson(
            self::USER_CONTROLLER_ROUTE,
            $registerData
        );
        $response->assertOk();

        $expectedData = [
            'name' => $registerData['name'],
            'surname' => $registerData['surname'],
            'email' => $registerData['email'],
            'phone' => $registerData['phone'],
            'default_address' => $registerData['default_address'],
        ];
        $response->assertJson($expectedData);
    }

    public function testRegisterWithInvalidDataShouldSuccess(): void
    {
        $registerData = [
            'name' => 'andrey',
            'surname' => 'prostoandrey',
            'email' => 'test2233@email.com',
            'password' => 'keK48!>O04780',
            'password_confirmation' => 'keK48!>O04780',
        ];
        $response = $this->postJson(
            self::USER_CONTROLLER_ROUTE,
            $registerData
        );
        $response->assertUnprocessable();
    }

    public function testRegisterWithExistedPhoneEmailShouldFail(): void
    {
        $user = $this->createUser();
        $registerData = [
            'name' => 'andrey',
            'surname' => 'prostoandrey',
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => 'keK48!>O04780',
            'password_confirmation' => 'keK48!>O04780',
            'default_address' => 'г. Москва',
        ];
        $response = $this->postJson(
            self::USER_CONTROLLER_ROUTE,
            $registerData
        );
        $response->assertUnprocessable();
    }
}
