<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductMeta;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchema;
use VCComponent\Laravel\Product\Test\TestCase;
use VCComponent\Laravel\User\Entities\User;

class AdminProductTypeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_field_meta_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        factory(ProductSchema::class)->create(['product_type' => 'sim']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/field-meta');

        $schemas = ProductSchema::where('product_type', 'sim')->get()->map(function ($item) {
            return [
                'id'             => $item->id,
                'name'           => $item->name,
                'label'          => $item->label,
                'schema_type_id' => $item->schema_type_id,
                'schema_rule_id' => $item->schema_rule_id,
                'product_type'   => $item->product_type,
                'timestamps'     => [
                    'created_at' => $item->created_at->toJSON(),
                    'updated_at' => $item->updated_at->toJSON(),
                ],
            ];
        })->toArray();

        $response->assertStatus(200);
        $response->assertJson(['data' => $schemas]);
    }

    /**
     * @test
     */
    public function can_bulk_force_delete_products_by_admin_with_type()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/force/bulk', $data);

        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_bulk_soft_products_to_trash_by_admin_router_with_type()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertSoftDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_force_delete_a_product_by_admin_with_type()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/' . $product['id'] . '/force');

        $response->assertJson(['success' => true]);
        $this->assertDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_delete_all_trash_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/trash/all');

        $response->assertJsonCount(5, 'data');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/trash/all');
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_bulk_delete_products_type_trash_by_admin()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/trash/bulk', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/trash/all');
        $response->assertJsonCount(5, 'data');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/trash/bulk', $data);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_delete_a_products_type_trash_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/trash/' . $product['id']);
        $response->assertJson(['message' => 'Product not found']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/' . $product['id']);

        $this->assertSoftDeleted('products', $product);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/trash/' . $product['id']);
        $response->assertJson(['success' => true]);

        $this->assertDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_get_trash_list_of_products_type_with_no_paginate_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/' . $product['id']);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/trash/all');
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
    public function can_get_trash_list_of_products_type_with_paginate_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/' . $product['id']);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/trash');
        $response->assertJsonStructure([
            'data' => [],
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
    public function can_bulk_restore_products_type_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertSoftDeleted('products', $item);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/products/trash/bulk/restores', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/products/' . $item['id']);
            $response->assertStatus(200);
            $response->assertJson(['data' => $item]);
        }
    }

    /**
     * @test
     */
    public function can_restore_a_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('products', $product);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/trash/' . $product['id'] . '/restore');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/' . $product['id']);
        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);
    }

    /**
     * @test
     */
    public function can_get_list_of_products_type_with_no_paginate_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/all');
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
    public function can_bulk_update_status_products_type_by_admin()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ['ids' => $listIds, 'status' => 5];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_type_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_create_sim_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        $data = factory(Product::class)->state('sim')->make()->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/sim', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_sim_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $id              = $product['id'];
        $product['name'] = 'update name';
        $data            = $product;

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $id, $data);

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
    public function can_delete_sim_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('DELETE', 'api/product-management/admin/sim/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_get_product_type_item_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/' . $product->id);

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
    public function can_get_product_type_list_by_admin_router()
    {
        $token = $this->loginToken();

        $products = factory(Product::class, 5)->state('sim')->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /**
     * @test
     */
    public function can_change_published_date_a_product_type_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $data     = ['published_date' => date('Y-m-d', strtotime('20-10-2020'))];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/date', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_check_stock_a_product_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/' . $product['id'] . '/stock');

        $response->assertStatus(200);
        $response->assertJson(['in_stock' => true]);

        $product  = factory(Product::class)->make(['quantity' => 0])->toArray();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/sim', $product);

        $productId = $response->decodeResponseJson()['data']['id'];
        $response  = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/' . $productId . '/stock');
        $response->assertJson(['in_stock' => false]);
    }

    /**
     * @test
     */
    public function can_update_quantity_a_product_type_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $number   = rand(1, 1000);
        $data     = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/quantity', $data);

        $response->assertJson(['quantity' => $data['quantity'] + $product['quantity']]);
    }

    /**
     * @test
     */
    public function can_change_quantity_a_product_type_by_admin()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $number   = rand(1, 1000);
        $data     = ['quantity' => $number];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/change_quantity', $data);
        $response->assertJson(['quantity' => $data['quantity']]);
    }

    /**
     * @test
     */
    public function can_export_product_type_by_admin_router()
    {
        $token = $this->loginToken();

        $product = factory(Product::class)->state('sim')->create();

        $data  = [$product];
        $param = '?label=product&extension=xlsx';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/product-management/admin/sim/exports' . $param);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJson(['data' => [[
            "Tên sản phẩm"    => $product->name,
            "Số lượng"        => $product->quantity,
            "Số lượng đã bán" => $product->sold_quantity,
            "Mã sản phẩm"     => $product->code,
            "Link ảnh"        => $product->thumbnail,
            "Gía bán"         => $product->price,
            "Đơn vị tính"     => $product->unit_price,
        ]]]);
    }

    /** @test */
    public function can_create_chema_when_create_product_of_type_sim_by_admin()
    {
        $token = $this->loginToken();

        $schemas = factory(ProductSchema::class, 2)->state('sim')->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . '_value';
        }

        $product = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/sim', $product);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_create_undefinded_chema_when_create_product_of_type_sim_by_admin()
    {
        $token = $this->loginToken();

        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefine_schema_value',
        ];

        $product = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('POST', 'api/product-management/admin/sim', $product);

        unset($product['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_create_new_schema_when_update_product_of_type_sim_by_admin()
    {
        $token = $this->loginToken();

        $schemas = factory(ProductSchema::class, 1)->state('sim')->create();

        $product_meta_datas = [];
        foreach ($schemas as $schema) {
            $product_meta_datas[$schema->name] = $schema->name . "_value";
        }

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $update_product_data = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $product['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_update_existed_schema_when_update_product_of_type_sim_by_admin()
    {
        $token = $this->loginToken();

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

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/' . $product[0]['id'], $update_product_data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $update_product_data]);

        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseHas('product_meta', ['key' => $key, 'value' => $value]);
        }
    }

    /** @test */
    public function can_skip_update_undefined_schema_when_update_product_of_type_sim_by_admin() {
        $token = $this->loginToken();

        $product_meta_datas = [
            'an_undefined_schema_key' => 'undefined_schema_value'
        ];

        $product = factory(Product::class)->state('sim')->create()->toArray();

        $new_data_with_undefined_schema = factory(Product::class)->state('sim')->make($product_meta_datas)->toArray();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('PUT', 'api/product-management/admin/sim/'.$product['id'], $new_data_with_undefined_schema);

        unset($new_data_with_undefined_schema['an_undefined_schema_key']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $new_data_with_undefined_schema]);
        
        foreach ($product_meta_datas as $key => $value) {
            $this->assertDatabaseMissing('product_meta', ['key' => $key, 'value' => $value]);
        }
    }
}
