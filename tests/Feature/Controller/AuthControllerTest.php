<?php

namespace Tests\Feature\Controller;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Assert;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Response;
use Tests\TestCase;

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
    public function testLogin(array $requestData, Closure $assertions): void
    {
        $this->createRegisteredUser();

        $response = $this->postJson('/api/login', $requestData);
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
    public function testLogout(array $requestData, Closure $assertions): void
    {
        $this->createRegisteredUser();

        $response = $this->postJson('/api/logout', $requestData);
        $decodedResponse = $response->decodeResponseJson();

        $assertions($response, $decodedResponse);
    }

    public static function logoutProvider(): array
    {
        return [
            'logoutWithValidTokensShouldSuccess' => [
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

    private function createRegisteredUser(): void
    {
        $user = new User([
            'name' => 'andrey',
            'surname' => 'prostoandrey',
            'email' => 'test2233@email.com',
            'phone' => '79987871698',
            'password' => bcrypt('keK48!>O04780'),
            'default_address' => 'г. Москва',
            'is_admin' => 0,
        ]);
        $user->save();
    }
}
