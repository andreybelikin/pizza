<?php

namespace Tests\Feature\Controller\User;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    #[DataProvider('contextDataProvider')]
    public function testUpdateUserShouldSuccess(\Closure $user, string $route): void
    {
        $user = $user($this);
        $updateUserData = [
            'name' => 'testName',
            'surname' => 'testSurname',
            'email' => 'shvartznigger@gmail.com',
            'phone' => '89963254558',
            'defaultAddress' => 'г. Магнитогорск',
        ];

        $response = $this->putJson(
            route($route, ['user' => $user->getKey()]),
            $updateUserData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );
        $expectedResult = $updateUserData;
        $expectedResult['default_address'] = $expectedResult['defaultAddress'];
        unset($expectedResult['defaultAddress']);

        $response->assertOk();
        $response->assertJson($expectedResult);
    }

    public function testUpdateAnotherUserByAdminShouldSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->getAnotherUser();
        $updateUserData = [
            'name' => 'testName',
            'surname' => 'testSurname',
            'email' => 'shvartznigger@gmail.com',
            'phone' => '89963254558',
            'defaultAddress' => 'г. Магнитогорск',
        ];

        $response = $this->putJson(
            route('admin.users.update', ['user' => $anotherUser->getKey()]),
            $updateUserData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $expectedResult = $updateUserData;
        $expectedResult['default_address'] = $expectedResult['defaultAddress'];
        unset($expectedResult['defaultAddress']);

        $response->assertOk();
        $response->assertJson($expectedResult);
    }

    public function testAnotherUserShouldFail()
    {
        $user = $this->getUser();
        $anotherUser = $this->getAnotherUser();
        $updateUserData = [
            'name' => 'testName',
            'surname' => 'testSurname',
            'email' => 'shvartznigger@gmail.com',
            'phone' => '89963254558',
            'defaultAddress' => 'г. Магнитогорск',
        ];

        $response = $this->putJson(
            route('users.update', ['user' => $anotherUser->getKey()]),
            $updateUserData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    #[DataProvider('contextDataProvider')]
    public function testUpdateUserWithInvalidDataShouldFail(\Closure $user, string $route)
    {
        $user = $user($this);
        $updateUserData = [
            'name' => 'testName',
            'surname' => 'testSurname',
            'email' => 'shvartznigger@gmail.com',
            'phone' => 'invalid phone number',
            'defaultAddress' => 'г. Магнитогорск',
        ];

        $response = $this->putJson(
            route($route, ['user' => $user->getKey()]),
            $updateUserData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertUnprocessable();
    }

    #[DataProvider('contextDataProvider')]
    public function testUpdateNonExistentUserShouldFail(\Closure $user, string $route)
    {
        $user = $user($this);
        $updateUserData = [
            'name' => 'testName',
            'surname' => 'testSurname',
            'email' => 'shvartznigger@gmail.com',
            'phone' => '89963254558',
            'defaultAddress' => 'г. Магнитогорск',
        ];

        $response = $this->putJson(
            route($route, ['user' => 9999]),
            $updateUserData,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertNotFound();
    }


    #[DataProvider('contextDataProvider')]
    public function testUpdateUserWithInvalidTokenShouldFail(\Closure $user, string $route)
    {
        $user = $user($this);
        $updateUserData = [
            'name' => 'testName',
            'surname' => 'testSurname',
            'email' => 'shvartznigger@gmail.com',
            'phone' => '89963254558',
            'defaultAddress' => 'г. Магнитогорск',
        ];

        $response = $this->putJson(
            route($route, ['user' => $user->getKey()]),
            $updateUserData,
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => 'users.update',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.users.update',
            ]
        ];
    }
}
