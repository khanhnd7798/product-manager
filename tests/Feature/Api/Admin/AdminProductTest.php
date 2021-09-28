<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductMeta;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchema;
use VCComponent\Laravel\Product\Test\Stubs\Models\Tag;
use VCComponent\Laravel\Product\Test\Stubs\Models\User;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_products_with_paginate_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['data' => $listProducts]);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_products_with_from_request_and_paginate_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?from=2021-12-12&field=created');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?from=2021-12-12&field=updated');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?from=2021-12-12&field=published');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function should_not_get_list_products_with_from_request_paginate_by_admin_router_without_field()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?from=2021-7-29');
        $response->assertStatus(500);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['message' => "field requied"]);
    }

    /**
     * @test
     */
    public function should_not_get_paginated_list_products_with_invalid_from_data_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?from=Hanoi, Vietname&field=created');

        $response->assertStatus(500);
    }


    /**
     * @test
     */
    public function can_get_list_products_with_to_request_and_paginate_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?to=2021-12-12&field=created');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?to=2021-12-12&field=updated');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?to=2021-12-12&field=published');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function should_not_get_list_products_with_to_request_paginate_by_admin_router_without_field()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?to=2021-7-29');
        $response->assertStatus(500);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['message' => "field requied"]);
    }

    /**
     * @test
     */
    public function should_not_get_paginated_list_products_with_invalid_to_data_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?to=Hanoi, Vietname&field=created');

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function can_get_list_in_stock_products_with_paginate_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create([
                'quantity' => 1
            ])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?in_stock=true');
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertStatus(200);
        $response->assertJson(['data' => $listProducts]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_out_stock_products_with_paginate_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create([
                'quantity' => 0
            ])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?in_stock=false');
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertStatus(200);
        $response->assertJson(['data' => $listProducts]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_status_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 2; $i < $number + 2; $i++) {
            $product = factory(Product::class)->create([
                'status' => $i,
            ])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?status=' . $listProducts[0]['status']);

        $response->assertStatus(200);
        $response->assertJson(['data' => [$listProducts[0]]]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_category_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create()->each(function ($product) {
            $product->categories()->save(factory(Category::class)->make([
                'slug' => 'demo_slug'
            ]));
        });

        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?categories=demo_slug');

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => $products,
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_tags_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create()->each(function ($product) {
            $product->tags()->save(factory(Tag::class)->make([
                'slug' => 'demo_slug'
            ]));
        });

        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?tags=demo_slug');

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson([
            'data' => $products,
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_author_id_by_admin_router()
    {
        $token = $this->loginToken();

        $author = factory(User::class)->create();
        $products = factory(Product::class, 5)->create([
            'author_id' => $author->id
        ]);

        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?author_id=' . $author->id);

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $products,
        ]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_constraints_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create();
        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $constraints = '{"name":"' . $products[0]['name'] . '","price":"' . $products[0]['price'] . '"}';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$products[0]],
        ]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_search_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create();

        $search = $products[0]['name'];

        $products = DB::table('products')->where('name', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%')->orWhere('price', 'like', '%'.$search.'%')->get();
        $products = $products->map(function ($product) {
            $product = (array) $product;
            unset($product['created_at']);
            unset($product['updated_at']);
            unset($product['deleted_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $products,
        ]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_paginated_products_with_order_by_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create();
        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?order_by={"name":"DESC"}');

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);


        $listNames = array_column($products, 'name');
        array_multisort($listNames, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$products[0]],
        ]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_show_list_product_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all');
        $response->assertStatus(200);

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['data' => $listProducts]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_from_request_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?from=2021-12-12&field=created');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => []
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?from=2021-12-12&field=updated');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?from=2021-12-12&field=published');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @test
     */
    public function should_not_get_list_all_products_with_from_request_without_field_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?from=2021-7-29');
        $response->assertStatus(500);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['message' => "field requied"]);
    }

    /**
     * @test
     */
    public function should_not_get_list_all_products_with_invalid_from_data_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?from=Hanoi, Vietname&field=created');

        $response->assertStatus(500);
    }


    /**
     * @test
     */
    public function can_get_list_all_products_with_to_request_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?to=2021-12-12&field=created');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?to=2021-12-12&field=updated');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?to=2021-12-12&field=published');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJsonStructure([
            'data' => [],
        ]);
    }

    /**
     * @test
     */
    public function should_not_get_list_all_products_with_to_request_by_admin_router_without_field()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?to=2021-7-29');
        $response->assertStatus(500);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['message' => "field requied"]);
    }

    /**
     * @test
     */
    public function should_not_get_list_all_products_with_invalid_to_data_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products?to=Hanoi, Vietname&field=created');

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function can_get_list_all_in_stock_products__by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create([
                'quantity' => 1
            ])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?in_stock=true');
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertStatus(200);
        $response->assertJson(['data' => $listProducts]);
        $response->assertJsonStructure([
            'data' => [],
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_out_stock_products_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create([
                'quantity' => 0
            ])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?in_stock=false');
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertStatus(200);
        $response->assertJson(['data' => $listProducts]);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_status_by_admin_router()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 2; $i < $number + 2; $i++) {
            $product = factory(Product::class)->create([
                'status' => $i,
            ])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?status=' . $listProducts[0]['status']);

        $response->assertStatus(200);
        $response->assertJson(['data' => [$listProducts[0]]]);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_category_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create()->each(function ($product) {
            $product->categories()->save(factory(Category::class)->make([
                'slug' => 'demo_slug'
            ]));
        });

        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?categories=demo_slug');

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);
        $response->assertJson([
            'data' => $products,
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_tags_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create()->each(function ($product) {
            $product->tags()->save(factory(Tag::class)->make([
                'slug' => 'demo_slug'
            ]));
        });

        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?tags=demo_slug');

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);
        $response->assertJson([
            'data' => $products,
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_author_id_by_admin_router()
    {
        $token = $this->loginToken();

        $author = factory(User::class)->create();
        $products = factory(Product::class, 5)->create([
            'author_id' => $author->id
        ]);

        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?author_id=' . $author->id);

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $products,
        ]);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_constraints_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create();
        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $constraints = '{"name":"' . $products[0]['name'] . '","price":"' . $products[0]['price'] . '"}';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$products[0]],
        ]);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_search_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create();

        $search = $products[0]['name'];

        $products = DB::table('products')->where('name', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%')->orWhere('price', 'like', '%'.$search.'%')->get();
        $products = $products->map(function ($product) {
            $product = (array) $product;
            unset($product['created_at']);
            unset($product['updated_at']);
            unset($product['deleted_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $products
        ]);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @test
     */
    public function can_get_list_all_products_with_order_by_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create();
        $products = $products->map(function ($product) {
            unset($product['created_at']);
            unset($product['updated_at']);
            return $product;
        })->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all?order_by={"name":"DESC"}');

        $listIds = array_column($products, 'id');
        array_multisort($listIds, SORT_DESC, $products);


        $listNames = array_column($products, 'name');
        array_multisort($listNames, SORT_DESC, $products);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$products[0]],
        ]);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /** @test */
    public function can_show_product_by_id_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/' . $product->id);

        $data = $product->toArray();
        unset($data['updated_at']);
        unset($data['created_at']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function should_not_show_product_with_indefined_id_admin_router()
    {
        $token = $this->loginToken();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/' . "NOT_A_PRODUCT_ID");

        $response->assertStatus(400);
        $response->assertJson(['message' => "products not found"]);
    }

    /** @test */
    public function can_create_product_by_admin_router()
    {
        $token = $this->loginToken();

        $data = factory(Product::class)->make()->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function can_create_product_with_customize_status_by_admin_router()
    {
        $token = $this->loginToken();

        $customise_status = 1;

        $data = factory(Product::class)->make([
            'status' => $customise_status
        ])->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function can_create_product_with_customize_original_price_by_admin_router()
    {
        $token = $this->loginToken();

        $customise_original_price = "200000";

        $data = factory(Product::class)->make([
            'original_price' => $customise_original_price
        ])->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function can_create_product_with_customize_slug_by_admin_router()
    {
        $token = $this->loginToken();

        $customise_slug = "custom-slug";

        $data = factory(Product::class)->make([
            'slug' => $customise_slug
        ])->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function can_create_product_with_duplicated_slug_by_admin_router()
    {
        $token = $this->loginToken();

        factory(Product::class)->create([
            'slug' => "existed-slug"
        ])->toArray();

        $data = factory(Product::class)->make([
            'slug' => 'existed-slug'
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function should_not_create_product_without_name_by_admin_router()
    {
        $token = $this->loginToken();

        $data = factory(Product::class)->make([
            'name' => null
        ])->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'name' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_product_without_price_by_admin_router()
    {
        $token = $this->loginToken();

        $data = factory(Product::class)->make([
            'price' => null
        ])->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'price' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_product_with_existed_sku_price_by_admin_router()
    {
        $token = $this->loginToken();

        factory(Product::class)->create([
            'sku' => 'EXISTEDSKUCODE'
        ]);
        $data = factory(Product::class)->make([
            'sku' => 'EXISTEDSKUCODE'
        ])->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'sku' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_update_product_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create();

        $id = $product->id;
        $product->name = 'update name';
        $data = $product->toArray();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . $id, $data);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
            ],
        ]);

        unset($data['updated_at']);
        unset($data['created_at']);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function should_not_update_product_with_indefined_id_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create();

        $id = $product->id;
        $product->name = 'update name';
        $data = $product->toArray();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . "NOT_A_PRODUCT_ID", $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);
    }

    /**
     * @test
     */
    public function should_not_update_product_without_name_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create();

        unset($product['name']);

        $data = $product->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . $data['id'], $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'name' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_not_update_product_without_price_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create();

        $product['price'] = null;

        $data = $product->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . $data['id'], $data);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function should_not_update_product_with_existed_sku_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 2)->create();

        $products[0]->sku = $products[1]->sku;

        $data = $products[0]->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . $data['id'], $data);

        $response->assertStatus(500);
    }


    /**
     * @test
     */
    public function can_get_field_meta_product_by_admin()
    {
        $token = $this->loginToken();

        factory(ProductSchema::class)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/field-meta');
        $response->assertStatus(200);

        $schemas = ProductSchema::get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'label' => $item->label,
                'schema_type_id' => $item->schema_type_id,
                'schema_rule_id' => $item->schema_rule_id,
                'product_type' => $item->product_type,
                'timestamps' => [
                    'created_at' => $item->created_at->toJSON(),
                    'updated_at' => $item->updated_at->toJSON(),
                ],
            ];
        })->toArray();

        $response->assertJson([
            'data' => $schemas,
        ]);
    }

    /**
     * @test
     */
    public function can_bulk_update_status_products_by_admin()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
            $this->assertDatabaseHas('products', $product);
        }

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        $data = ['ids' => $listIds, 'status' => 5];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function should_not_bulk_update_status_products_without_status_by_admin()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
            $this->assertDatabaseHas('products', $product);
        }

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        $data = ['ids' => $listIds];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/status/bulk', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure(['errors' => [
            'status' => []
        ]]);
    }

    /**
     * @test
     */
    public function should_not_bulk_update_status_products_without_ids_by_admin()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
            $this->assertDatabaseHas('products', $product);
        }

        $data = ['status' => 5];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/status/bulk', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'ids' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_not_bulk_update_status_products_with_undefined_ids_by_admin()
    {
        $token = $this->loginToken();

        $number = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
            $this->assertDatabaseHas('products', $product);
        }

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        $listIds = array_merge($listIds, [0, 99]);
        $data = ['ids' => $listIds, 'status' => 5];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/status/bulk', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Products not found']);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data = ['status' => 2];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function should_not_update_status_a_product_without_status_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data = [];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/status', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'status' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_not_update_status_a_product_with_undefine_id_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data = ['status' => 1];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . 'UNDEFINE_PRODUCT_ID' . '/status', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => "Product not found"]);
    }

    /**
     * @test
     */
    public function can_change_published_date_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $data = ['published_date' => date('Y-m-d', strtotime('20-10-2020'))];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/date', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function should_not_change_published_date_with_undefine_product_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $data = ['published_date' => date('Y-m-d', strtotime('20-10-2020'))];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . 'UNDEFINE_PRODUCT_ID' . '/date', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);
    }

    /**
     * @test
     */
    public function should_not_change_published_date_with_invalid_published_data_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $data = ['published_date' => "INVALID_DATE"];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/date', $data);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function should_not_change_published_date_without_published_data_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $data = [];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/date', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'published_date' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_check_stock_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/product/' . $product['id'] . '/stock');

        $response->assertStatus(200);
        $response->assertJson(['in_stock' => true]);

        $product = factory(Product::class)->make(['quantity' => 0])->toArray();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $product);

        $productId = $response->decodeResponseJson()['data']['id'];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/product/' . $productId . '/stock');
        $response->assertJson(['in_stock' => false]);
    }



    /**
     * @test
     */
    public function should_not_check_stock_an_undefined_product_by_admin()
    {
        $token = $this->loginToken();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/product/' . 'UNDEFINED_ID' . '/stock');

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);
    }

    /**
     * @test
     */
    public function can_update_quantity_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $number = rand(1, 1000);
        $data = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/quantity', $data);

        $response->assertJson(['quantity' => $data['quantity'] + $product['quantity']]);
    }

    /**
     * @test
     */
    public function should_not_update_quantity_a_product_with_undefined_id_by_admin()
    {
        $token = $this->loginToken();

        $number = rand(1, 1000);
        $data = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . 'UNDEFINED_ID' . '/quantity', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);
    }

    /**
     * @test
     */
    public function should_not_update_quantity_a_product_with_nan_quantity_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $number = 'NOT_A_NUMBER_QUANTITY';
        $data = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/quantity', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'quantity' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_not_update_quantity_a_product_without_quantity_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();
        $data = [];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/quantity', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'quantity' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_change_quantity_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $number = rand(1, 1000);
        $data = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/change_quantity', $data);
        $response->assertJson(['quantity' => $data['quantity']]);
    }

    /**
     * @test
     */
    public function should_not_change_quantity_a_product_with_undefined_id_by_admin()
    {
        $token = $this->loginToken();

        $number = rand(1, 1000);
        $data = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . 'UNDEFINE_ID' . '/change_quantity', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);
    }

    /**
     * @test
     */
    public function should_not_change_quantity_a_product_with_nan_quantity_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();

        $number = "NOT_A_NUMBER";
        $data = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/change_quantity', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'quantity' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_not_change_quantity_a_product_without_quantity_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create()->toArray();
        $data = [];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/change_quantity', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'quantity' => []
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_get_product_type_by_admin()
    {
        $token = $this->loginToken();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/productTypes');

        $entity = new Product;
        $getProductTypes = $entity->productTypes();
        $response->assertJson([
            'data' => $getProductTypes,
        ]);
    }

    /**
     * @test
     */
    public function can_export_product_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->create();

        $data = [$product];
        $param = '?label=product&extension=xlsx';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJson(['data' => [[
            "Tên sản phẩm" => $product->name,
            "Số lượng" => $product->quantity,
            "Số lượng đã bán" => $product->sold_quantity,
            "Mã sản phẩm" => $product->code,
            "Link ảnh" => $product->thumbnail,
            "Gía bán" => $product->price,
            "Đơn vị tính" => $product->unit_price,
        ]]]);
    }

    /**
     * @test
     */
    public function can_export_product_created_from_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $products = factory(Product::class, 5)->create([
            "created_at" => $date
        ]);

        $param = '?label=product&extension=xlsx&field=created&from=' . $date;

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_updated_from_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $products = factory(Product::class, 5)->create([
            "updated_at" => $date
        ]);

        $param = '?label=product&extension=xlsx&field=updated&from=' . $date;

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_published_from_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $products = factory(Product::class, 5)->create([
            "published_date" => $date
        ]);

        $param = '?label=product&extension=xlsx&field=published&from=' . $date;

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function should_not_export_product_from_date_without_field_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $param = '?label=product&extension=xlsx&&from=' . $date;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function should_not_export_product_from_invalid_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = 'INVALID_DATE_FOMART';

        $param = '?label=product&extension=xlsx&field=published&from=' . $date;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function can_export_product_created_to_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $products = factory(Product::class, 5)->create([
            "created_at" => $date
        ]);

        $param = '?label=product&extension=xlsx&field=created&to=' . $date;

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_updated_to_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $products = factory(Product::class, 5)->create([
            "updated_at" => $date
        ]);

        $param = '?label=product&extension=xlsx&field=updated&to=' . $date;

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_published_to_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $products = factory(Product::class, 5)->create([
            "published_date" => $date
        ]);

        $param = '?label=product&extension=xlsx&field=published&to=' . $date;

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function should_not_export_product_to_date_without_field_by_admin_router()
    {
        $token = $this->loginToken();

        $date = date('Y-m-d', strtotime('30-7-2021'));

        $param = '?label=product&extension=xlsx&&to=' . $date;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function should_not_export_product_to_invalid_date_by_admin_router()
    {
        $token = $this->loginToken();

        $date = 'INVALID_DATE_FOMART';

        $param = '?label=product&extension=xlsx&field=published&to=' . $date;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function can_export_products_with_in_stock_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create([
            'quantity' => rand(0, 1)
        ]);

        $products = $products->filter(function ($product) {
            return $product->quantity > 0;
        });

        $param = '?label=product&extension=xlsx&in_stock=true';

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_products_with_out_stock_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create([
            'quantity' => rand(0, 1)
        ]);

        $products = $products->filter(function ($product) {
            return $product->quantity < 1;
        });

        $param = '?label=product&extension=xlsx&in_stock=false';

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_products_with_status_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 2)->create([
            'status' => 1
        ]);

        $products = $products->filter(function ($product) {
            return $product->status = 1;
        });

        $param = '?label=product&extension=xlsx&status=1';

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_products_with_category_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->create([
            'status' => rand(0, 1)
        ])->each(function ($product) {
            $product->categories()->save(factory(Category::class)->make([
                'slug' => 'demo_slug'
            ]));
        });

        $param = '?label=product&extension=xlsx&category=demo_slug';

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);

        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_with_constraints_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class, 5)->create();

        $param = '?label=product&extension=xlsx&constraints={"name":"' . $product[0]->name . '","description":"' . $product[0]->description . '"}';

        $export_products = $this->getExportProducts(collect([$product[0]]));

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_with_search_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class, 5)->create();

        $search = $product[0]->name;

        $param = '?label=product&extension=xlsx&search=' . $search;

        $products = DB::table('products')->where('name', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%')->orWhere('price', 'like', '%'.$search.'%')->get();
        $products = $products->map(function ($product) {
            return $product;
        });

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /**
     * @test
     */
    public function can_export_product_with_author_id_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class, 5)->create([
            'author_id' => rand(1, 2)
        ]);

        $param = '?label=product&extension=xlsx&search=1';

        $products = $product->filter(function ($product) {
            return $product->author_id == 1;
        });

        $export_products = $this->getExportProducts($products);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJson(['data' => $export_products]);
    }

    /** @test */
    public function can_create_chema_when_create_product_by_admin()
    {
        $token = $this->loginToken();

        $schemas = factory(ProductSchema::class, 2)->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . '_value';
        }

        $product = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $product);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_create_undefinded_chema_when_create_product_by_admin()
    {
        $token = $this->loginToken();

        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefine_schema_value',
        ];

        $product = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/products', $product);

        unset($product['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_create_new_schema_when_update_product_by_admin()
    {
        $token = $this->loginToken();

        $schemas = factory(ProductSchema::class, 1)->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . "_value";
        }

        $product = factory(Product::class)->create()->toArray();

        $update_product_data = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . $product['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_update_existed_schema_when_update_product_by_admin()
    {
        $token = $this->loginToken();

        $schemas = factory(ProductSchema::class, 1)->create();

        $product_meta_datas = [];
        $product_metas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . "_value";

            array_push($product_metas, factory(ProductMeta::class)->make([
                'key' => $schema->name,
                'value' => ""
            ]));
        }

        $product = factory(Product::class)->create()->productMetas()->saveMany($product_metas);

        $update_product_data = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/' . $product[0]['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_update_undefined_schema_when_update_product_by_admin() {
        $token = $this->loginToken();

        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefined_schema_value'
        ];

        $product = factory(Product::class)->create()->toArray();

        $new_data_with_undefined_schema = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/'.$product['id'], $new_data_with_undefined_schema);

        unset($new_data_with_undefined_schema['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $new_data_with_undefined_schema]);
        
        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    protected function getExportProducts($products): array
    {
        return $products->map(function ($product) {
            $export_product = [];
            $export_product["Tên sản phẩm"] = $product->name;
            $export_product["Số lượng"] = $product->quantity;
            $export_product["Số lượng đã bán"] = $product->sold_quantity;
            $export_product["Mã sản phẩm"] = $product->code;
            $export_product["Link ảnh"] = $product->thumbnail;
            $export_product["Gía bán"] = $product->price;
            $export_product["Đơn vị tính"] = $product->unit_price;
            return $export_product;
        })->toArray();
    }
}
