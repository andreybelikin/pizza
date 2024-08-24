<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\DatabaseTransactions;
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
}
