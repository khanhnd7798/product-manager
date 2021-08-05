<?php

namespace VCComponent\Laravel\Product\Test\Feature\Web\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use VCComponent\Laravel\Product\Test\TestCase;

class WebProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_products_by_web_router()
    {
        $products = factory(Product::class)->create()->toArray();
        unset($products['updated_at']);
        unset($products['created_at']);

        $response = $this->call('GET', 'product-management/products');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");
    }

    /**
     * @test
     */
    public function can_get_list_products_with_constraints_by_web_router()
    {
        $products = factory(Product::class, 5)->create();

        $constraint = $products[0]['description'];

        $products = $products->filter(function ($product) use ($constraint) {
            return $product['description'] == $constraint;
        })->toArray();

        $response = $this->call('GET', 'product-management/products?constraints={"description":"' . $constraint . '"}');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");

        $this->assertResponseHasProduct($response, $products);
    }

    /**
     * @test
     */
    public function can_get_list_products_with_search_by_web_router()
    {
        $products = factory(Product::class, 3)->create();

        $search = $products[0]->name;

        $products = $products->filter(function ($product) use ($search) {
            return $product['name'] == $search || $product['price'] == $search;
        })->toArray();

        $response = $this->call('GET', 'product-management/products?search=' . $search);

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");

        $this->assertResponseHasProduct($response, $products);
    }

    /**
     * @test
     */
    public function can_get_list_products_with_order_by_by_web_router()
    {
        $products = factory(Product::class, 5)->create()->toArray();

        $listNames = array_column($products, 'name');
        array_multisort($listNames, SORT_DESC, $products);

        $response = $this->call('GET', 'product-management/products?order_by={"name":"DESC"}');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");

        $this->assertResponseHasProduct($response, $products);
    }

    /**
     * @test
     */
    public function can_get_a_product_by_web_router()
    {

        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'product-management/products/' . $product['slug']);

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-detail");
        $response->assertViewHasAll([
            'product.name'     => $product['name'],
            'product.slug'     => $product['slug'],
            'product.quantity' => $product['quantity'],
            'product.price'    => $product['price'],
        ]);
    }


    /**
     * @test
     */
    public function can_get_list_products_type_by_web_router()
    {

        $products = factory(Product::class)->state('sim')->create()->toArray();
        unset($products['updated_at']);
        unset($products['created_at']);

        $response = $this->call('GET', 'product-management/sim');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");
    }

    /**
     * @test
     */
    public function can_get_list_products_type_with_constraints_by_web_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $constraint = $products[0]['description'];

        $products = $products->filter(function ($product) use ($constraint) {
            return $product['description'] == $constraint;
        })->toArray();

        $response = $this->call('GET', 'product-management/sim?constraints={"description":"' . $constraint . '"}');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");

        $this->assertResponseHasProduct($response, $products);
    }

    /**
     * @test
     */
    public function can_get_list_products_type_with_search_by_web_router()
    {
        $products = factory(Product::class, 3)->state('sim')->create();

        $search = $products[0]->name;

        $products = $products->filter(function ($product) use ($search) {
            return $product['name'] == $search || $product['price'] == $search;
        })->toArray();

        $response = $this->call('GET', 'product-management/sim?search=' . $search);

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");

        $this->assertResponseHasProduct($response, $products);
    }

    /**
     * @test
     */
    public function can_get_list_products_type_with_order_by_by_web_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create()->toArray();

        $listNames = array_column($products, 'name');
        array_multisort($listNames, SORT_DESC, $products);

        $response = $this->call('GET', 'product-management/sim?order_by={"name":"DESC"}');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");

        $this->assertResponseHasProduct($response, $products);
    }

    /**
     * @test
     */
    public function can_get_a_product_type_by_web_router()
    {

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'product-management/sim/' . $product['slug']);

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-detail");
        $response->assertViewHasAll([
            'product.name'     => $product['name'],
            'product.slug'     => $product['slug'],
            'product.quantity' => $product['quantity'],
            'product.price'    => $product['price'],
        ]);
    }

    protected function assertResponseHasProduct($response, $products)
    {
        $response_post = $response['products'];
        $this->assertEquals($response_post->count(), count($products));
        for ($i = 0; $i < count($products); $i++) {
            $this->assertEquals($response_post[$i]->id, $products[$i]['id']);
            $this->assertEquals($response_post[$i]->slug, $products[$i]['slug']);
            $this->assertEquals($response_post[$i]->name, $products[$i]['name']);
            $this->assertEquals($response_post[$i]->description, $products[$i]['description']);
            $this->assertEquals($response_post[$i]->price, $products[$i]['price']);
        }
    }
}
