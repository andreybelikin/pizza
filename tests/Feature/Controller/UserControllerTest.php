<?php

namespace Tests\Feature\Controller;

use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestData\TestUser;
use Tests\TestData\Tokens;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private const CONTROLLER_ROUTE = '/api/user/';

    public function testGetUserSuccess(): void
    {
        $user = TestUser::createUserForToken();
        $accessToken = Tokens::generateAccessToken($user->email, TestUser::$plainPassword);

        $response = $this->getJson(
            self::CONTROLLER_ROUTE . $user->getKey(),
            ['authorization' => 'Bearer ' . $accessToken]
        );

        $response->assertOk();
        $response->assertJson([
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'phone' => $user->phone,
            'default_address' => $user->default_address,
        ]);
    }

    #[DataProvider('getUserFailedProvider')]
    public function testGetUserFailed(
        bool $anotherUserId,
        ?string $token,
        Closure $assertions
    ): void {
        $user = TestUser::createUserForToken();
        $userId = $anotherUserId ? $user->getKey() + 1 : $user->getKey();

        $accessToken = $token ?? Tokens::generateAccessToken($user->email, TestUser::$plainPassword);

        $response = $this->getJson(
            self::CONTROLLER_ROUTE . $userId,
            ['authorization' => 'Bearer ' . $accessToken]
        );

        $assertions($response);
    }

    public static function getUserFailedProvider(): array
    {
        return [
            'accessPolicyViolation' => [
                'anotherUserId' => true,
                'token' => null,
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_NOT_FOUND);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('Resource is not exist', $decodedResponse['message']);
                },
            ],
            'invalidToken' => [
                'anotherUserId' => false,
                'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('Token Signature could not be verified.', $decodedResponse['message']);
                },
            ],
        ];
    }

    public function testUpdateUserSuccess(): void
    {
        $user = TestUser::createUserForToken();
        $accessToken = Tokens::generateAccessToken($user->email, TestUser::$plainPassword);

        $updateUserData = [
            'name' => 'testName',
            'email' => 'shvartznigger@gmail.com',
            'default_address' => 'г. Магнитогорск',
        ];

        $response = $this->patchJson(
            self::CONTROLLER_ROUTE . $user->getKey(),
            $updateUserData,
            ['authorization' => 'Bearer ' . $accessToken]
        );

        $response->assertOk();
        $response->assertJson([
            'name' => $updateUserData['name'],
            'surname' => $user->surname,
            'email' => $updateUserData['email'],
            'phone' => $user->phone,
            'default_address' => $updateUserData['default_address'],
        ]);
    }

    #[DataProvider('userUpdateFailureProvider')]
    public function testUpdateUserFailure(
        array $updateUserData,
        bool $anotherUserId,
        ?string $token,
        Closure $assertions,
    ): void {
        $user = TestUser::createUserForToken();
        $userId = $anotherUserId ? $user->getKey() + 1 : $user->getKey();

        $accessToken = $token ?? Tokens::generateAccessToken($user->email, TestUser::$plainPassword);

        $response = $this->patchJson(
            self::CONTROLLER_ROUTE . $userId,
            $updateUserData,
            ['authorization' => 'Bearer ' . $accessToken]
        );

        $assertions($response);
    }

    public static function userUpdateFailureProvider(): array
    {
        return [
            'invalidRequest' => [
                'updateUserData' => [
                    'phone' => 'invalid phone number',
                    'name' => 'shvartznigger@gmail.com',
                ],
                'anotherUserId' => false,
                'token' => null,
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_BAD_REQUEST);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('The given data failed to pass validation', $decodedResponse['message']);
                },
            ],
            'accessPolicyViolation' => [
                'anotherUserId' => true,
                'token' => null,
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_NOT_FOUND);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('Resource is not exist', $decodedResponse['message']);
                },
            ],
            'invalidToken' => [
                'anotherUserId' => false,
                'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('Token Signature could not be verified.', $decodedResponse['message']);
                },
            ],
        ];
    }
}
