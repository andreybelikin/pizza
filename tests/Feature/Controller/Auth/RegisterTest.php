<?php

namespace Tests\Feature\Controller\Auth;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testRegisterShouldSuccess(string $route): void
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
            $route,
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

    #[DataProvider('contextDataProvider')]
    public function testRegisterWithInvalidDataShouldSuccess(string $route): void
    {
        $registerData = [
            'name' => 'andrey',
            'surname' => 'prostoandrey',
            'email' => 'test2233@email.com',
            'password' => 'keK48!>O04780',
            'password_confirmation' => 'keK48!>O04780',
        ];
        $response = $this->postJson(
            $route,
            $registerData
        );
        $response->assertUnprocessable();
    }

    #[DataProvider('contextDataProvider')]
    public function testRegisterWithExistedPhoneEmailShouldFail(string $route): void
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
            $route,
            $registerData
        );
        $response->assertUnprocessable();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'route' => route('auth.register'),
            ],
            'admin' => [
                'route' => route('admin.auth.register'),
            ]
        ];
    }
}
