<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use VCComponent\Laravel\Product\Test\Stubs\Models\Variant;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminVariantTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_get_list_all_variants_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $variants = $variants->map(function ($variant) {
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return $variant;
        })->toArray();

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants/list');

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
    }

    /** @test */
    public function can_get_list_all_variants_with_constraints_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $constraint_label = $variants[0]->label;
        $constraint_price = $variants[0]->price;

        $variants = $variants->filter(function ($variant) use ($constraint_label, $constraint_price) {
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return ($variant['label'] == $constraint_label && $variant['price'] == $constraint_price);
        })->toArray();

        $constraints = '{"label":"' . $constraint_label . '", "price":"' . $constraint_price . '"}';

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants/list?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
    }

    /** @test */
    public function can_get_list_all_variants_with_search_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $search = $variants[0]->label;

        $variants = DB::table('variants')->where('label', 'like', '%'.$search.'%')->orWhere('price', 'like', '%'.$search.'%')->orWhere('type', 'like', '%'.$search.'%')->get();
        $variants = $variants->map(function ($variant) {
            $variant = (array) $variant;
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return $variant;
        })->toArray();

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants/list?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
    }

    /** @test */
    public function can_get_list_all_variants_with_order_by_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $variants = $variants->map(function ($variant) {
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return $variant;
        })->toArray();

        $order_by = '{"label":"DESC"}';

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $listLabels = array_column($variants, 'label');
        array_multisort($listLabels, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants/list?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
    }

    /** @test */
    public function can_get_list_pagiante_variants_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $variants = $variants->map(function ($variant) {
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return $variant;
        })->toArray();

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants');

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /** @test */
    public function can_get_list_paginate_variants_with_constraints_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $constraint_label = $variants[0]->label;
        $constraint_price = $variants[0]->price;

        $variants = $variants->filter(function ($variant) use ($constraint_label, $constraint_price) {
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return ($variant['label'] == $constraint_label && $variant['price'] == $constraint_price);
        })->toArray();

        $constraints = '{"label":"' . $constraint_label . '", "price":"' . $constraint_price . '"}';

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /** @test */
    public function can_get_list_paginate_variants_with_search_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $search = $variants[0]->label;

        $variants = DB::table('variants')->where('label', 'like', '%'.$search.'%')->orWhere('price', 'like', '%'.$search.'%')->orWhere('type', 'like', '%'.$search.'%')->get();
        $variants = $variants->map(function ($variant) {
            $variant = (array) $variant;
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return $variant;
        })->toArray();

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /** @test */
    public function can_get_list_paginate_variants_with_order_by_by_admin()
    {
        $variants = factory(Variant::class, 5)->create();

        $variants = $variants->map(function ($variant) {
            unset($variant['created_at']);
            unset($variant['updated_at']);
            return $variant;
        })->toArray();

        $order_by = '{"label":"DESC"}';

        $listIds = array_column($variants, 'id');
        array_multisort($listIds, SORT_DESC, $variants);

        $listLabels = array_column($variants, 'label');
        array_multisort($listLabels, SORT_DESC, $variants);

        $response = $this->call('GET', 'api/product-management/admin/variants?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variants]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /** @test */
    public function can_get_a_variant_with_order_by_by_admin()
    {
        $variant = factory(Variant::class)->create()->toArray();

        unset($variant['created_at']);
        unset($variant['updated_at']);

        $response = $this->call('GET', 'api/product-management/admin/variants/' . $variant['id']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variant]);
    }

    /** @test */
    public function should_not_get_a_variant_with_undefine_id_by_admin()
    {

        $response = $this->call('GET', 'api/product-management/admin/variants/' . rand(1, 5));

        $response->assertStatus(500);
        $response->assertJson(['message' => "Không tìm thấy giá trị !"]);
    }

    /** @test */
    public function can_create_a_variant_by_admin()
    {
        $data = factory(Variant::class)->make()->toArray();

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->call('POST', 'api/product-management/admin/variants', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /** @test */
    public function should_not_create_a_variant_with_null_label_by_admin()
    {
        $data = factory(Variant::class)->make([
            'label' => null
        ])->toArray();

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->call('POST', 'api/product-management/admin/variants', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'label' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_a_variant_with_null_product_id_by_admin()
    {
        $data = factory(Variant::class)->make([
            'product_id' => null
        ])->toArray();

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->call('POST', 'api/product-management/admin/variants', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'product_id' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_a_variant_by_admin()
    {
        $variant = factory(Variant::class)->create();
        $variant->label = "new_label";
        $variant = $variant->toArray();

        unset($variant['updated_at']);
        unset($variant['created_at']);

        $response = $this->call('PUT', 'api/product-management/admin/variants/' . $variant['id'], $variant);

        $response->assertStatus(200);
        $response->assertJson(['data' => $variant]);
    }

    /** @test */
    public function should_not_update_a_variant_with__undefine_id_by_admin()
    {
        $variant = factory(Variant::class)->make()->toArray();

        $response = $this->call('PUT', 'api/product-management/admin/variants/' . rand(1, 5), $variant);

        $response->assertStatus(500);
        $response->assertJson(['message' => "Không tìm thấy thuộc tính !"]);
    }

    /** @test */
    public function should_not_update_a_variant_with_null_label_by_admin()
    {
        $variant = factory(Variant::class)->create();
        $variant->label = null;
        $variant = $variant->toArray();

        unset($variant['updated_at']);
        unset($variant['created_at']);

        $response = $this->call('PUT', 'api/product-management/admin/variants/' . $variant['id'], $variant);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'label' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_update_a_variant_with_null_product_id_by_admin()
    {
        $variant = factory(Variant::class)->create();
        $variant->product_id = null;
        $variant = $variant->toArray();

        unset($variant['updated_at']);
        unset($variant['created_at']);

        $response = $this->call('PUT', 'api/product-management/admin/variants/' . $variant['id'], $variant);

        $response->assertStatus(422);
        $response->assertJson(['message' => "The given data was invalid."]);
        $response->assertJsonStructure([
            'errors' => [
                'product_id' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_status_variant_by_admin()
    {
        $update_status = 1;

        $variant = factory(Variant::class)->create();
        $variant->status = $update_status;
        $variant = $variant->toArray();

        unset($variant['updated_at']);
        unset($variant['created_at']);

        $response = $this->call('PUT', 'api/product-management/admin/variant/' . $variant['id'] . '/status', ['status' => $update_status]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function should_not_update_status_variant_with_undefined_id_by_admin()
    {
        $response = $this->call('PUT', 'api/product-management/admin/variant/' . rand(1, 5) . '/status', ['status' => rand(1, 5)]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Không tìm thấy thuộc tính !']);
    }

    /** @test */
    public function should_not_update_status_variant_with_out_status_by_admin()
    {
        $variant = factory(Variant::class)->create();
        $variant = $variant->toArray();

        $response = $this->call('PUT', 'api/product-management/admin/variant/' . $variant['id'] . '/status', []);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'status' => []
            ]
        ]);
    }

    /** @test */
    public function can_delete_a_variant_by_admin()
    {
        $variant = factory(Variant::class)->create();

        $response = $this->call('DELETE', 'api/product-management/admin/variants/' . $variant['id']);

        // $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
