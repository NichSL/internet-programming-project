<?php

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access cart api', function () {
    $response = $this->getJson('/api/cart');

    $response->assertStatus(401);
});

test('user can view their empty cart', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/cart');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'items' => [],
                'totalItems' => 0,
                'totalPrice' => 0,
            ],
        ]);
});

test('user can add a product to their cart', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller', 'store_name' => 'Toko ABC']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'price' => 50000, 'stock' => 10]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/cart', [
        'productId' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'totalItems' => 2,
                'totalPrice' => 100000,
            ],
        ]);

    expect(CartItem::where('user_id', $user->id)->count())->toBe(1);
});

test('adding the same product increments quantity without duplicate row', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'price' => 50000, 'stock' => 10]);

    Sanctum::actingAs($user);

    $this->postJson('/api/cart', ['productId' => $product->id, 'quantity' => 2])->assertStatus(200);
    $this->postJson('/api/cart', ['productId' => $product->id, 'quantity' => 3])->assertStatus(200);

    expect(CartItem::where('user_id', $user->id)->count())->toBe(1);

    $item = CartItem::where('user_id', $user->id)->first();
    expect($item->quantity)->toBe(5);
});

test('cannot add product with quantity exceeding available stock', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'price' => 50000, 'stock' => 5]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/cart', [
        'productId' => $product->id,
        'quantity' => 10,
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
        ]);
});

test('cannot add out of stock product', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'stock' => 0]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/cart', [
        'productId' => $product->id,
        'quantity' => 1,
    ]);

    $response->assertStatus(400);
});

test('user can update quantity of item in their cart', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'price' => 20000, 'stock' => 15]);

    $cartItem = CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    Sanctum::actingAs($user);

    $response = $this->patchJson("/api/cart/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'totalItems' => 5,
                'totalPrice' => 100000,
            ],
        ]);

    expect($cartItem->fresh()->quantity)->toBe(5);
});

test('cannot update quantity beyond available stock', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'stock' => 5]);

    $cartItem = CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    Sanctum::actingAs($user);

    $response = $this->patchJson("/api/cart/{$cartItem->id}", [
        'quantity' => 10,
    ]);

    $response->assertStatus(400);
});

test('user cannot update another users cart item', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'stock' => 10]);

    $cartItemUser1 = CartItem::create([
        'user_id' => $user1->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    Sanctum::actingAs($user2);

    $response = $this->patchJson("/api/cart/{$cartItemUser1->id}", [
        'quantity' => 4,
    ]);

    $response->assertStatus(404);
    expect($cartItemUser1->fresh()->quantity)->toBe(2);
});

test('user can delete an item from their cart', function () {
    $user = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'price' => 30000, 'stock' => 10]);

    $cartItem = CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/cart/{$cartItem->id}");

    $response->assertStatus(200);
    expect(CartItem::find($cartItem->id))->toBeNull();
});

test('user cannot delete another users cart item', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'stock' => 10]);

    $cartItemUser1 = CartItem::create([
        'user_id' => $user1->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    Sanctum::actingAs($user2);

    $response = $this->deleteJson("/api/cart/{$cartItemUser1->id}");

    $response->assertStatus(404);
    expect(CartItem::find($cartItemUser1->id))->not->toBeNull();
});

test('user can clear their own cart without affecting other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create(['user_id' => $seller->id, 'stock' => 10]);

    CartItem::create([
        'user_id' => $user1->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $itemUser2 = CartItem::create([
        'user_id' => $user2->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    Sanctum::actingAs($user1);

    $response = $this->deleteJson('/api/cart');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'items' => [],
                'totalItems' => 0,
                'totalPrice' => 0,
            ],
        ]);

    expect(CartItem::where('user_id', $user1->id)->count())->toBe(0);
    expect(CartItem::where('user_id', $user2->id)->count())->toBe(1);
});
