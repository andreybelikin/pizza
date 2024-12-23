<?php

namespace Tests\Feature\Controller\Cart;

use App\Enums\Limit\CartProductLimit;
use App\Exceptions\Limit\QuantityPerTypeLimitException;
use App\Exceptions\Resource\ResourceAccessException;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Exceptions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CartUpdateTest extends TestCase
{
    private const CONTROLLER_ROUTE = '/api/users/{userId}/carts';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testUpdateCartByOwnerSuccess(): void
    {
        $user = $this->createUser();
        $this->createCartProducts($user);
        $updateRequest = $this->getCartUpdateRequest($user, 3);
        $expectedResponse = $this->getExpectedResponse($updateRequest);

        $response = $this->putJson(
            str_replace('{userId}', $user->getKey(), self::CONTROLLER_ROUTE),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedResponse);
    }

    public function testUpdateCartByAdminSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->createUser();
        $this->createCartProducts($anotherUser);
        $updateRequest = $this->getCartUpdateRequest($anotherUser, 3);
        $expectedResponse = $this->getExpectedResponse($updateRequest);

        $response = $this->putJson(
            str_replace('{userId}', $anotherUser->getKey(), self::CONTROLLER_ROUTE),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedResponse);
    }

    public function testUpdateCartWithInvalidTokenShouldFail(): void
    {
        $user = $this->createUser();
        $this->createCartProducts($user);
        $updateRequest = $this->getCartUpdateRequest($user, 3);

        $response = $this->putJson(
            str_replace('{userId}', $user->getKey(), self::CONTROLLER_ROUTE),
            $updateRequest,
            ['authorization' => 'Bearer ' . self::$this->getInvalidToken()]
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $decodedResponse = $response->decodeResponseJson();

        static::assertArrayHasKey('message', $decodedResponse);
        static::assertSame('Token Signature could not be verified.', $decodedResponse['message']);
    }

    public function testUpdateAnotherUserCartShouldFail(): void
    {
        $user = $this->createUser();
        $anotherUser = $this->createUser();
        $this->createCartProducts($anotherUser);
        $updateRequest = $this->getCartUpdateRequest($anotherUser, 3);

        Exceptions::fake();
        $response = $this->putJson(
            str_replace('{userId}', $anotherUser->getKey(), self::CONTROLLER_ROUTE),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        Exceptions::assertReported(ResourceAccessException::class);
    }

    public function testUpdateCartWithViolatedLimitsShouldFail(): void
    {
        $user = $this->createUser();
        $this->createCartProducts($user);
        $updateRequest = $this->getCartUpdateRequest($user, 25);

        Exceptions::fake();
        $response = $this->putJson(
            str_replace('{userId}', $user->getKey(), self::CONTROLLER_ROUTE),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Exceptions::assertReported(QuantityPerTypeLimitException::class);
    }

    private function getCartUpdateRequest(User $user, int $newQuantity): array
    {
        $request = [];
        $cartProducts = $user->products()
            ->distinct()
            ->get()
            ->toArray();
        $newCartProduct = Product::query()
            ->whereIn('type', CartProductLimit::getLimits())
            ->whereNotIn('id', array_column($cartProducts, 'id'))
            ->first()
            ->toArray();
        $cartProducts = [...$cartProducts, $newCartProduct];

        foreach ($cartProducts as $product) {
            $request['products'][] = [
                'id' => $product['id'],
                'quantity' => $newQuantity,
            ];
        }

        return $request;
    }

    private function getExpectedResponse(array $cartUpdateRequest): array
    {
        $responseProducts = array_map(function ($requestProduct) {
            $product = Product::query()->find($requestProduct['id']);

            return [
                'id' => $requestProduct['id'],
                'quantity' => $requestProduct['quantity'],
                'title' => $product['title'],
                'price' => floor($product['price']),
                'totalPrice' => $product['price'] * $requestProduct['quantity'],
            ];
        }, $cartUpdateRequest['products']);

        return [
            'products' => $responseProducts,
            'totalSum' => array_sum(array_column($responseProducts, 'totalPrice')),
        ];
    }
}
