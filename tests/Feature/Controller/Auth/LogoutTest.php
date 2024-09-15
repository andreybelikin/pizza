<?php

namespace Tests\Feature\Controller\Auth;

use App\Services\TokenBlacklistService;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Assert;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\TestData\TestUser;
use Tests\TestData\Tokens;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        TestUser::createAuthorizedUser();
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
                    $refreshToken = Tokens::generateRefreshToken();

                    return [
                        'authorization' => sprintf('Bearer %s', auth()->getToken()),
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
                    $accessToken = auth()->getToken();
                    $refreshToken = Tokens::generateRefreshToken();

                    auth()->user()->delete();
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
                    $refreshToken = Tokens::generateRefreshToken();

                    $tokenBlacklistService = app(TokenBlacklistService::class);

                    $hashedToken = hash('sha256', auth()->getToken());
                    $tokenBlacklistService->add($hashedToken);

                    return [
                        'authorization' => sprintf('Bearer %s', auth()->getToken()),
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
}
