<?php

namespace Tests\Feature\Controller\Cart;

use App\Enums\Limit\CartProductLimit;
use App\Models\Product;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CartUpdateTest extends TestCase
{
    public function testUpdateCartByOwnerShouldSuccess(): void
    {
        $user = $this->createUser();
        $this->createCartProducts($user);
        $updateRequest = $this->getCartUpdateRequest($user, 3);
        $expectedResponse = $this->getExpectedResponse($updateRequest);

        $response = $this->putJson(
            route('users.cart.update', ['userId' => $user->getKey()]),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedResponse);
    }

    public function testUpdateAnotherUserCartByAdminShouldSuccess(): void
    {
        $user = $this->getAdminUser();
        $anotherUser = $this->createUser();
        $this->createCartProducts($anotherUser);
        $updateRequest = $this->getCartUpdateRequest($anotherUser, 3);
        $expectedResponse = $this->getExpectedResponse($updateRequest);

        $response = $this->putJson(
            route('admin.users.cart.update', ['userId' => $anotherUser->getKey()]),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertOk();
        $response->assertJson($expectedResponse);
    }

    #[DataProvider('contextDataProvider')]
    public function testUpdateCartWithInvalidTokenShouldFail(\Closure $user, string $route): void
    {
        $user = $user($this);
        $this->createCartProducts($user);
        $updateRequest = $this->getCartUpdateRequest($user, 3);

        $response = $this->putJson(
            route($route, ['userId' => $user->getKey()]),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getInvalidToken()]
        );

        $response->assertUnauthorized();
    }

    public function testUpdateAnotherUserCartShouldFail(): void
    {
        $user = $this->createUser();
        $anotherUser = $this->createUser();
        $this->createCartProducts($anotherUser);
        $updateRequest = $this->getCartUpdateRequest($anotherUser, 3);

        $response = $this->putJson(
            route('users.cart.update', ['userId' => $anotherUser->getKey()]),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertForbidden();
    }

    #[DataProvider('contextDataProvider')]
    public function testUpdateCartWithViolatedLimitsShouldFail(\Closure $user, string $route): void
    {
        $user = $user($this);
        $this->createCartProducts($user);
        $updateRequest = $this->getCartUpdateRequest($user, 25);

        $response = $this->putJson(
            route($route, ['userId' => $user->getKey()]),
            $updateRequest,
            ['authorization' => 'Bearer ' . $this->getUserAccessToken($user)]
        );

        $response->assertUnprocessable();
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
                'price' => $product['price'],
                'totalPrice' => $product['price'] * $requestProduct['quantity'],
            ];
        }, $cartUpdateRequest['products']);

        return [
            'products' => $responseProducts,
            'totalSum' => array_sum(array_column($responseProducts, 'totalPrice')),
        ];
    }

    public static function contextDataProvider(): array
    {
        return [
            'user' => [
                'user' => fn ($self) => $self->getUser(),
                'route' => 'users.cart.update',
            ],
            'admin' => [
                'user' => fn ($self) => $self->getAdminUser(),
                'route' => 'admin.users.cart.update',
            ]
        ];
    }
}
