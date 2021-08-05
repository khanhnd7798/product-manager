<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductMeta;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchema;
use VCComponent\Laravel\Product\Test\TestCase;

class FrontEndProductTypeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_of_products_type_with_no_paginate_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'api/product-management/sim/all');
        $response->assertJsonMissingExact([
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson(['data' => [$product]]);
    }

     /**
     * @test
     */
    public function can_bulk_update_status_products_type_by_frontend_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();
        
        $listIds = array_column($products, 'id');
        $data    = ['ids' => $listIds, 'status' => 5];

        $response = $this->json('GET', 'api/product-management/sim/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->json('PUT', 'api/product-management/sim/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/sim/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_type_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/product-management/sim/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/sim/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_create_sim_product_type_by_frontend_router()
    {
        $data = factory(Product::class)->state('sim')->make()->toArray();

        $response = $this->json('POST', 'api/product-management/sim', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_sim_product_type_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $id              = $product['id'];
        $product['name'] = 'update name';
        $data            = $product;

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->json('PUT', 'api/product-management/sim/' . $id, $data);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
            ],
        ]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_delete_sim_product_type_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/sim/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_get_product_type_item_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'api/product-management/sim/' . $product->id);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name'         => $product->name,
                'description'  => $product->description,
                'product_type' => 'sim',
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_product_type_list_by_frontend_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $response = $this->call('GET', 'api/product-management/sim');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function can_create_chema_when_create_product_of_type_sim_by_frontent_router()
    {
        $schemas = factory(ProductSchema::class, 2)->state('sim')->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . '_value';
        }

        $product = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->call('POST', 'api/product-management/sim', $product);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_create_undefinded_chema_when_create_product_of_type_sim_by_frontent_router()
    {
        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefine_schema_value',
        ];

        $product = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->call('POST', 'api/product-management/sim', $product);

        unset($product['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_create_new_schema_when_update_product_of_type_sim_by_frontent_router()
    {
        $schemas = factory(ProductSchema::class, 1)->state('sim')->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . "_value";
        }

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $update_product_data = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->call('PUT', 'api/product-management/sim/' . $product['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_update_existed_schema_when_update_product_of_type_sim_by_frontent_router()
    {
        $schemas = factory(ProductSchema::class, 1)->state('sim')->create();

        $product_meta_datas = [];
        $product_metas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . "_value";

            array_push($product_metas, factory(ProductMeta::class)->make([
                'key' => $schema->name,
                'value' => ""
            ]));
        }

        $product = factory(Product::class)->state('sim')->create()->productMetas()->saveMany($product_metas);

        $update_product_data = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->call('PUT', 'api/product-management/sim/' . $product[0]['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_update_undefined_schema_when_update_product_of_type_sim_by_frontent_router() {
        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefined_schema_value'
        ];

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $new_data_with_undefined_schema = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->call('PUT', 'api/product-management/sim/'.$product['id'], $new_data_with_undefined_schema);

        unset($new_data_with_undefined_schema['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $new_data_with_undefined_schema]);
        
        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }
}
