<?php

namespace Tests\Feature\Controller;

use App\Models\User;
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

    private const ROUTE = '/api/user/';


    public function testGetUserSuccess(): void
    {
        $user = TestUser::createUserForToken();
        $accessToken = Tokens::generateAccessToken($user->email, TestUser::$plainPassword);

        $response = $this->getJson(self::ROUTE . $user->getKey(), ['authorization' => 'Bearer ' . $accessToken]);

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
    ): void
    {
        $user = TestUser::createUserForToken();
        $userId = $anotherUserId ? $user->getKey() + 1 : $user->getKey();

        $accessToken = $token ?? Tokens::generateAccessToken($user->email, TestUser::$plainPassword);

        $response = $this->getJson(self::ROUTE . $userId, ['authorization' => 'Bearer ' . $accessToken]);

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

        $newUserData = [
            'name' => 'testName',
            'email' => 'shvartznigger@gmail.com',
            'default_address' => 'г. Магнитогорск',
        ];

        $response = $this->patchJson(self::ROUTE . $user->getKey(), $newUserData, ['authorization' => 'Bearer ' . $accessToken]);

        $response->assertOk();
        $response->assertJson([
            'name' => $newUserData['name'],
            'surname' => $user->surname,
            'email' => $newUserData['email'],
            'phone' => $user->phone,
            'default_address' => $newUserData['default_address'],
        ]);
    }
}
