<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductMeta;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchema;
use VCComponent\Laravel\Product\Test\TestCase;

class FrontEndProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_products_frontend_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create(['product_type' => 'products'])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->call('GET', 'api/product-management/products/all');

        $response->assertStatus(200);

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['data' => $listProducts]);
    }

    /**
     * @test
     */
    public function can_bulk_update_status_products_frontend_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create(['product_type' => 'products'])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
            $this->assertDatabaseHas('products', $product);
        }

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        $data    = ['ids' => $listIds, 'status' => 5];

        $response = $this->json('GET', 'api/product-management/products/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->json('PUT', 'api/product-management/products/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/products/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    
    public function can_update_status_a_product_frontend_router()
    {
        $product = factory(Product::class)->create(['product_type' => 'products'])->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/product-management/product/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/products/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_get_list_products_with_paginate_frontend_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create(['product_type' => 'products'])->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->call('GET', 'api/product-management/products');

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
    public function can_show_product_by_id_frontend_router()
    {
        $product = factory(Product::class)->create();

        $response = $this->json('GET', 'api/product-management/products/' . $product->id);

        $data = $product->toArray();
        unset($data['updated_at']);
        unset($data['created_at']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_create_product_by_frondend_router()
    {
        $data = factory(Product::class)->make()->toArray();

        $response = $this->json('POST', 'api/product-management/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_product_by_frondend_router()
    {
        $product = factory(Product::class)->create();

        $id            = $product->id;
        $product->name = 'update name';
        $data          = $product->toArray();
        $response      = $this->json('PUT', 'api/product-management/products/' . $id, $data);

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

    /** @test */
    public function can_create_chema_when_create_product_by_frontend_router()
    {
        $schemas = factory(ProductSchema::class, 2)->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . '_value';
        }

        $product = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->call('POST', 'api/product-management/products', $product);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_create_undefinded_chema_when_create_product_by_frontend_router()
    {
        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefine_schema_value',
        ];

        $product = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->call('POST', 'api/product-management/products', $product);

        unset($product['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_create_new_schema_when_update_product_by_frontend_router()
    {
        $schemas = factory(ProductSchema::class, 1)->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . "_value";
        }

        $product = factory(Product::class)->create()->toArray();

        $update_product_data = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->call('PUT', 'api/product-management/products/' . $product['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_update_existed_schema_when_update_product_by_frontend_router()
    {
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

        $response = $this->call('PUT', 'api/product-management/products/' . $product[0]['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_update_undefined_schema_when_update_product_by_frontend_router() {
        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefined_schema_value'
        ];

        $product = factory(Product::class)->create(['product_type' => 'products'])->toArray();

        $new_data_with_undefined_schema = factory(Product::class)->make($product_meta_datas)->toArray();

        $response = $this->call('PUT', 'api/product-management/products/'.$product['id'], $new_data_with_undefined_schema);

        unset($new_data_with_undefined_schema['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $new_data_with_undefined_schema]);
        
        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }
}
