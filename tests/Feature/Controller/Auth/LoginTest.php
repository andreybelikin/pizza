<?php

namespace Tests\Feature\Controller\Auth;

use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Illuminate\Testing\Assert;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\UserTrait;

class LoginTest extends TestCase
{
    #[DataProvider('loginProvider')]
    public function testLogin(array $requestCredentials, Closure $assertions): void
    {
        parent::setUp();
        $initialCredentials = [
            'email' => 'test2233@email.com',
            'password' => 'keK48!>O04780',
        ];
        $this->createUserWithCredentials($initialCredentials);

        $response = $this->postJson('/api/login', $requestCredentials);
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
}
