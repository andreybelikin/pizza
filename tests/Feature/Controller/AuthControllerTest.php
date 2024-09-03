<?php

namespace Tests\Feature\Controller;

use App\Models\User;
use App\Services\Token\TokenBlacklistService;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Assert;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\TestData\TestUser;
use Tests\TestData\Tokens;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('registerProvider')]
    public function testRegister(array $requestData, array $responseAssertions): void
    {
        $response = $this->postJson('/api/register', $requestData);
        $decodedResponse = $response->decodeResponseJson();

        $response->assertStatus($responseAssertions['status']);

        if ($responseAssertions['status'] !== Response::HTTP_OK) {
            $this->assertArrayHasKey('errors', $decodedResponse);
        }

        $this->assertSame($responseAssertions['message'], $decodedResponse['message']);
    }

    public static function registerProvider(): array
    {
        return [
            'registrationWithValidDataShouldSuccess' => [
                [
                    'name' => 'andrey',
                    'surname' => 'prostoandrey',
                    'email' => 'test2233@email.com',
                    'phone' => '79987871698',
                    'password' => 'keK48!>O04780',
                    'password_confirmation' => 'keK48!>O04780',
                    'default_address' => 'г. Москва',
                ],
                [
                    'status' => Response::HTTP_OK,
                    'message' => 'User successfully registered',
                ],
            ],
            'registrationWithInvalidDataShouldFail' => [
                [
                    'name' => 'andrey',
                    'surname' => 'prostoandrey',
                    'email' => 'test2233@email.com',
                    'password' => 'keK48!>O04780',
                    'password_confirmation' => 'keK48!>O04780',
                ],
                [
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => 'The given data failed to pass validation',
                ],
            ],
        ];
    }

    #[DataProvider('loginProvider')]
    public function testLogin(array $credentials, Closure $assertions): void
    {
        TestUser::createUserWithCredentials($credentials);

        $response = $this->postJson('/api/login', $credentials);
        $decodedResponse = $response->decodeResponseJson();

        $assertions($response, $decodedResponse);
    }

    public static function loginProvider(): array
    {
        return [
            'loginWithValidAndMatchingCredentialsShouldSuccess' => [
                [
                    'email' => 'test2233@email.com',
                    'password' => 'keK48!>O04780',
                ],
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_OK);
                    Assert::assertArrayHasKey('accessToken', $decodedResponse);
                    Assert::assertArrayHasKey('refreshToken', $decodedResponse);
                    Assert::assertNotEmpty($decodedResponse['accessToken']);
                    Assert::assertNotEmpty($decodedResponse['accessToken']);
                },
            ],
            'loginWithInvalidCredentialsShouldFail' => [
                [
                    'email' => 2233,
                    'password' => 'keK48!>O04780',
                ],
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_BAD_REQUEST);
                    Assert::assertArrayHasKey('message', $decodedResponse);
                    Assert::assertSame(
                        'The given data failed to pass validation',
                        $decodedResponse['message']
                    );
                },
            ],
            'loginWithMismatchedCredentialsShouldFail' => [
                [
                    'email' => 'test2233@email.RU',
                    'password' => 'keK48!>O04780',
                ],
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
                    Assert::assertArrayHasKey('message', $decodedResponse);
                    Assert::assertSame(
                        'User with these credentials is not exist',
                        $decodedResponse['message']
                    );
                },
            ],
        ];
    }

    #[DataProvider('logoutProvider')]
    public function testLogout(Closure $tokens, Closure $assertions): void
    {
        $response = $this->postJson('/api/logout', [], $tokens());
        $decodedResponse = $response->decodeResponseJson();

        $assertions($response, $decodedResponse);
    }

    public static function logoutProvider(): array
    {
        return [
            'logoutWithValidTokensShouldReturnSuccessControllerResponse' => [
                function () {
                    $user = TestUser::createUserForToken();
                    $accessToken = Tokens::generateAccessToken($user->email, TestUser::$plainPassword);
                    $refreshToken = Tokens::generateRefreshToken();

                    return [
                        'authorization' => sprintf('Bearer %s', $accessToken),
                        'x-refresh-token' => $refreshToken,
                    ];
                },
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_OK);
                    Assert::assertArrayHasKey('message', $decodedResponse);
                    Assert::assertSame('Successfully logged out', $decodedResponse['message']);
                },
            ],
            'logoutWithTokenUserNotDefinedReturnSuccessMiddlewareResponse' => [
                function () {
                    $user = TestUser::createUserForToken();
                    $accessToken = Tokens::generateAccessToken($user->email, TestUser::$plainPassword);
                    $refreshToken = Tokens::generateRefreshToken();

                    $user->delete();
                    auth()->logout();

                    return [
                        'authorization' => sprintf('Bearer %s', $accessToken),
                        'x-refresh-token' => $refreshToken,
                    ];
                },
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_OK);
                    Assert::assertArrayHasKey('message', $decodedResponse);
                    Assert::assertSame(
                        'Successfully logged out',
                        $decodedResponse['message']
                    );
                },
            ],
            'logoutWithBlacklistedAccessTokenReturnSuccessControllerResponse' => [
                function () {
                    $user = TestUser::createUserForToken();
                    $accessToken = Tokens::generateAccessToken($user->email, TestUser::$plainPassword);
                    $refreshToken = Tokens::generateRefreshToken();

                    $tokenBlacklistService = app(TokenBlacklistService::class);

                    $hashedToken = hash('sha256', $accessToken);
                    $tokenBlacklistService->add($hashedToken);

                    return [
                        'authorization' => sprintf('Bearer %s', $accessToken),
                        'x-refresh-token' => $refreshToken,
                    ];
                },
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_OK);
                    Assert::assertArrayHasKey('message', $decodedResponse);
                    Assert::assertSame('Successfully logged out', $decodedResponse['message']);
                },
            ],
        ];
    }

    #[DataProvider('refreshProvider')]
    public function testRefresh(Closure $createTokens, Closure $requestTokensAssertions, Closure $responseAssertions)
    {
        $requestTokens = $createTokens();
        $response = $this->postJson('/api/refresh', [], $requestTokens);
        $decodedResponse = $response->decodeResponseJson();

        $responseAssertions($response, $decodedResponse);
        $requestTokensAssertions($requestTokens);
    }

    public static function refreshProvider(): array
    {
        return [
            'refreshWithValidTokenShouldBlacklistRequestTokenAndReturnSuccessControllerResponse' => [
                function () {
                    $user = TestUser::createUserForToken();
                    Tokens::generateAccessToken($user->email, TestUser::$plainPassword);
                    $refreshToken = Tokens::generateRefreshToken();

                    return ['x-refresh-token' => $refreshToken];
                },
                function () {
                    Assert::assertEquals(1, count(DB::table('token_blacklist')->get()));
                },
                function (TestResponse $response, AssertableJsonString $decodedResponse) {
                    $response->assertStatus(Response::HTTP_OK);
                    Assert::assertArrayHasKey('accessToken', $decodedResponse);
                    Assert::assertArrayHasKey('refreshToken', $decodedResponse);
                },
            ],
        ];
    }
}
