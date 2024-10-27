<?php

namespace Tests\Feature\Controller\User;

use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\UserTrait;

class DeleteTest extends TestCase
{
    use DatabaseTransactions;

    private const CONTROLLER_ROUTE = '/api/user/';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        UserTrait::createAdminAuthorizedUser();
    }

    public function testDeleteUserSuccess(): void
    {
        $user = auth()->user();

        $response = $this->deleteJson(
            self::CONTROLLER_ROUTE . auth()->user()->getKey(),
            [],
            ['authorization' => 'Bearer ' . auth()->getToken()]
        );

        $response->assertOk();
        static::assertModelMissing($user);
    }

    #[DataProvider('userDeleteFailureProvider')]
    public function testDeleteUserFailure(
        ?Closure $anotherUserId,
        ?string  $invalidToken,
        Closure  $assertions,
    ): void {
        $authorizedUser = auth()->user();

        $userId = $anotherUserId ? $anotherUserId($authorizedUser) : $authorizedUser->getKey();
        $accessToken = $invalidToken ?? auth()->getToken();

        $response = $this->deleteJson(
            self::CONTROLLER_ROUTE . $userId,
            [],
            ['authorization' => 'Bearer ' . $accessToken]
        );

        $assertions($response);
        static::assertModelExists($authorizedUser);
    }

    public static function userDeleteFailureProvider(): array
    {
        return [
            'deleteNonExistentUserShouldFail' => [
                'anotherUserId' => fn() => 10000,
                'invalidToken' => null,
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_NOT_FOUND);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('Resource is not exist', $decodedResponse['message']);
                },
            ],
            'deleteAnotherUserShouldFail' => [
                'anotherUserId' => fn() => UserTrait::getAnotherUser()->getKey(),
                'invalidToken' => null,
                'assertions' => function (TestResponse $response) {
                    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
                    $decodedResponse = $response->decodeResponseJson();

                    static::assertArrayHasKey('message', $decodedResponse);
                    static::assertSame('Don\'t have permission to this resource', $decodedResponse['message']);
                },
            ],
            'deleteRequestWithInvalidTokenShouldFail' => [
                'anotherUserId' => null,
                'invalidToken' => 'eyJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEifQ.ZAU547bnCcGrvSZiaDeYpbQg6rUopOe3HMJ01l2a2NQ',
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
