<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use VCComponent\Laravel\Product\Test\Stubs\Models\Attribute;
use VCComponent\Laravel\Product\Test\Stubs\Models\AttributeValue;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminAttributeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_list_paginated_attributes_by_admin()
    {
        $attributes = factory(Attribute::class, 5)->create();

        $attributes = $attributes->map(function ($attribute) {
            unset($attribute['created_at']);
            unset($attribute['updated_at']);
            return $attribute;
        })->toArray();

        $listIds = array_column($attributes, 'id');
        array_multisort($listIds, SORT_DESC, $attributes);

        $response = $this->call('GET', 'api/product-management/admin/attributes');

        $response->assertStatus(200);
        $response->assertJson(['data' => $attributes]);
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
    public function can_get_list_paginated_attributes_with_constraints_by_admin()
    {
        $attributes = factory(Attribute::class, 5)->create();

        $constraint_name = $attributes[0]->name;

        $attributes = $attributes->filter(function ($attribute) use ($constraint_name) {
            unset($attribute['created_at']);
            unset($attribute['updated_at']);
            return $attribute->name == $constraint_name;
        })->toArray();

        $constraints = '{"name":"' . $constraint_name . '"}';

        $listIds = array_column($attributes, 'id');
        array_multisort($listIds, SORT_DESC, $attributes);

        $response = $this->call('GET', 'api/product-management/admin/attributes?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attributes]);
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
    public function can_get_list_paginated_attributes_with_search_by_admin()
    {
        $attributes = factory(Attribute::class, 5)->create();

        $search = $attributes[0]->name;

        $attributes = DB::table('attributes')->where('name', 'like', '%'.$search.'%')->get();
        $attributes = $attributes->map(function ($attribute) {
            $attribute = (array) $attribute;
            unset($attribute['created_at']);
            unset($attribute['updated_at']);
            return $attribute;
        })->toArray();

        $listIds = array_column($attributes, 'id');
        array_multisort($listIds, SORT_DESC, $attributes);

        $response = $this->call('GET', 'api/product-management/admin/attributes?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attributes]);
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
    public function can_get_list_paginated_attributes_with_order_by_by_admin()
    {
        $attributes = factory(Attribute::class, 5)->create();

        $attributes = $attributes->map(function ($attribute) {
            unset($attribute['created_at']);
            unset($attribute['updated_at']);
            return $attribute;
        })->toArray();

        $order_by = '{"name":"DESC"}';

        $listIds = array_column($attributes, 'id');
        array_multisort($listIds, SORT_DESC, $attributes);

        $listNames = array_column($attributes, 'name');
        array_multisort($listNames, SORT_DESC, $attributes);

        $response = $this->call('GET', 'api/product-management/admin/attributes?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attributes]);
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
    public function can_get_a_attribute_by_admin()
    {
        $attribute = factory(Attribute::class)->create()->toArray();

        unset($attribute['created_at']);
        unset($attribute['updated_at']);

        $response = $this->call('GET', 'api/product-management/admin/attributes/' . $attribute['id']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute]);
    }

    /** @test */
    public function should_not_get_a_attribute_with_undefined_id_by_admin()
    {
        $response = $this->call('GET', 'api/product-management/admin/attributes/' . rand(5, 7));

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Không tìm thấy thuộc tính !']);
    }

    /** @test */
    public function can_create_a_attribute_by_admin()
    {
        $data = factory(Attribute::class)->make()->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/attributes/', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /** @test */
    public function should_not_create_a_attribute_without_name_by_admin()
    {
        $data = factory(Attribute::class)->make([
            'name' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/attributes/', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'name' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_a_attribute_duplicated_name_by_admin()
    {
        $duplicated_name = "this_name_has_been_taken";

        factory(Attribute::class)->create([
            'name' => $duplicated_name
        ]);

        $data = factory(Attribute::class)->make([
            'name' => $duplicated_name
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/attributes/', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'name' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_a_attribute_without_type_by_admin()
    {
        $data = factory(Attribute::class)->make([
            'type' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/attributes/', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'type' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_a_attribute_without_kind_by_admin()
    {
        $data = factory(Attribute::class)->make([
            'kind' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/attributes/', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'kind' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_a_attribute_by_admin()
    {
        $attribute = factory(Attribute::class)->create();
        $attribute->name = "updated_name";
        $attribute->type = "updated_type";
        $attribute->kind = "updated_kind";
        $attribute = $attribute->toArray();

        unset($attribute['created_at']);
        unset($attribute['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attributes/' . $attribute['id'], $attribute);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute]);
    }



    /** @test */
    public function should_not_update_a_attribute_with_undefine_id_by_admin()
    {
        $attribute = factory(Attribute::class)->make()->toArray();

        $response = $this->call('PUT', 'api/product-management/admin/attributes/' . rand(1, 2), $attribute);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Không tìm thấy thuộc tính !']);
    }

    /** @test */
    public function should_not_update_a_attribute_without_name_by_admin()
    {
        $attribute = factory(Attribute::class)->create();
        $attribute->name = null;
        $attribute->type = "updated_type";
        $attribute->kind = "updated_kind";
        $attribute = $attribute->toArray();

        unset($attribute['created_at']);
        unset($attribute['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attributes/' . $attribute['id'], $attribute);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'name' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_update_a_attribute_duplicated_name_by_admin()
    {
        $duplicated_name = "this_name_has_been_taken";

        factory(Attribute::class)->create([
            'name' => $duplicated_name
        ]);

        $attribute = factory(Attribute::class)->create();
        $attribute->name = $duplicated_name;
        $attribute->type = "updated_type";
        $attribute->kind = "updated_kind";
        $attribute = $attribute->toArray();

        unset($attribute['created_at']);
        unset($attribute['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attributes/' . $attribute['id'], $attribute);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'name' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_update_a_attribute_without_type_by_admin()
    {
        $attribute = factory(Attribute::class)->create();
        $attribute->name = "updated_name";
        $attribute->type = null;
        $attribute->kind = "updated_kind";
        $attribute = $attribute->toArray();

        unset($attribute['created_at']);
        unset($attribute['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attributes/' . $attribute['id'], $attribute);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'type' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_update_a_attribute_without_kind_by_admin()
    {
        $attribute = factory(Attribute::class)->create();
        $attribute->name = "updated_name";
        $attribute->type = "updated_type";
        $attribute->kind = null;
        $attribute = $attribute->toArray();

        unset($attribute['created_at']);
        unset($attribute['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attributes/' . $attribute['id'], $attribute);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'kind' => []
            ]
        ]);
    }

    /** @test */
    public function can_delet_a_attribute_by_admin()
    {
        $attribute = factory(Attribute::class)->create()->toArray();

        $response = $this->call('DELETE', 'api/product-management/admin/attributes/' . $attribute['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDeleted('attributes', $attribute);
    }

    /** @test */
    public function can_get_list_paginated_attribute_values_by_admin()
    {
        $attribute_values = factory(AttributeValue::class, 5)->create();

        $attribute_values = $attribute_values->map(function ($attribute_value) {
            unset($attribute_value['created_at']);
            unset($attribute_value['updated_at']);
            return $attribute_value;
        })->toArray();

        $listIds = array_column($attribute_values, 'id');
        array_multisort($listIds, SORT_DESC, $attribute_values);

        $response = $this->call('GET', 'api/product-management/admin/attribute-value');

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute_values]);
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
    public function can_get_list_paginated_attribute_values_with_constraints_by_admin()
    {
        $attribute_values = factory(AttributeValue::class, 5)->create();

        $constraint_label = $attribute_values[0]->label;

        $attribute_values = $attribute_values->filter(function ($attribute_value) use ($constraint_label) {
            unset($attribute_value['created_at']);
            unset($attribute_value['updated_at']);
            return $attribute_value->label == $constraint_label;
        })->toArray();

        $constraints = '{"label":"' . $constraint_label . '"}';

        $listIds = array_column($attribute_values, 'id');
        array_multisort($listIds, SORT_DESC, $attribute_values);

        $response = $this->call('GET', 'api/product-management/admin/attribute-value?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute_values]);
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
    public function can_get_list_paginated_attribute_values_with_search_by_admin()
    {
        $attribute_values = factory(AttributeValue::class, 5)->create();

        $search = $attribute_values[0]->label;

        $attribute_values = DB::table('attribute_values')->where('label', 'like', '%'.$search.'%')->get();
        $attribute_values = $attribute_values->map(function ($attribute_value) {
            $attribute_value = (array) $attribute_value;
            unset($attribute_value['created_at']);
            unset($attribute_value['updated_at']);
            return $attribute_value;
        })->toArray();

        $listIds = array_column($attribute_values, 'id');
        array_multisort($listIds, SORT_DESC, $attribute_values);

        $response = $this->call('GET', 'api/product-management/admin/attribute-value?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute_values]);
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
    public function can_get_list_paginated_attribute_values_with_order_by_by_admin()
    {
        $attribute_values = factory(AttributeValue::class, 5)->create();

        $attribute_values = $attribute_values->map(function ($attribute_value) {
            unset($attribute_value['created_at']);
            unset($attribute_value['updated_at']);
            return $attribute_value;
        })->toArray();

        $order_by = '{"label":"DESC"}';

        $listIds = array_column($attribute_values, 'id');
        array_multisort($listIds, SORT_DESC, $attribute_values);

        $listLabels = array_column($attribute_values, 'label');
        array_multisort($listLabels, SORT_DESC, $attribute_values);

        $response = $this->call('GET', 'api/product-management/admin/attribute-value?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute_values]);
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
    public function can_get_a_attribute_value_by_admin()
    {
        $attribute_value = factory(AttributeValue::class)->create()->toArray();

        unset($attribute_value['created_at']);
        unset($attribute_value['updated_at']);

        $response = $this->call('GET', 'api/product-management/admin/attribute-value/' . $attribute_value['id']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $attribute_value]);
    }

    /** @test */
    public function should_not_get_an_undefine_attribute_value_by_admin()
    {
        $response = $this->call('GET', 'api/product-management/admin/attribute-value/' . rand(1, 5));

        $response->assertStatus(500);
        $response->assertJson(['message' => "Không tìm thấy giá trị !"]);
    }

    /** @test */
    public function can_create_a_attribute_value_by_admin()
    {
        $data = factory(AttributeValue::class)->make()->toArray();

        $response = $this->call('POST', 'api/product-management/admin/attribute-value', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /** @test */
    public function should_not_create_a_attribute_value_with_null_attibute_id_by_admin()
    {
        $data = factory(AttributeValue::class)->make([
            'attribute_id' => null
        ])->toArray();

        $response = $this->call('POST', 'api/product-management/admin/attribute-value', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'attribute_id' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_create_a_attribute_value_with_null_label_by_admin()
    {
        $data = factory(AttributeValue::class)->make([
            'label' => null
        ])->toArray();

        $response = $this->call('POST', 'api/product-management/admin/attribute-value', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'label' => []
            ]
        ]);
    }

    /** @test */
    // public function should_not_create_a_attribute_value_with_existed_attribute_id_by_admin()
    // {
    //     $exited_attribute_id = 9;
    //     factory(AttributeValue::class)->create([
    //         'attribute_id' => $exited_attribute_id
    //     ]);

    //     $data = factory(AttributeValue::class)->make([
    //         'attribute_id' => $exited_attribute_id
    //     ])->toArray();

    //     $response = $this->call('POST', 'api/product-management/admin/attribute-value', $data);
    //     $response->assertJson(['message' => 'Giá trị của thuộc tính này đã tồn tại !']);
    //     $response->assertStatus(500);
    // }

    /** @test */
    public function can_update_a_attribute_value_by_admin()
    {
        $attribute_value = factory(AttributeValue::class)->create();

        $attribute_value->label = "new_label";
        $attribute_value->value = "new_value";
        $attribute_value = $attribute_value->toArray();

        unset($attribute_value['created_at']);
        unset($attribute_value['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attribute-value/' . $attribute_value['id'], $attribute_value);

        // $response->assertStatus(200);
        $response->assertJson(['data' => $attribute_value]);
    }

    /** @test */
    public function should_not_update_an_undefined_attribute_valu_by_admin()
    {
        $data = factory(AttributeValue::class)->make()->toArray();
        $response = $this->call('PUT', 'api/product-management/admin/attribute-value/'.rand(1, 5), $data);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Không tìm thấy giá trị !']);
    }

    /** @test */
    public function should_not_update_attribute_value_with_null_attribute_id_by_admin()
    {
        $attribute_value = factory(AttributeValue::class)->create();

        $attribute_value->attribute_id = null;
        $attribute_value->label = "new_label";
        $attribute_value->value = "new_value";
        $attribute_value = $attribute_value->toArray();

        unset($attribute_value['created_at']);
        unset($attribute_value['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attribute-value/' . $attribute_value['id'], $attribute_value);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'attribute_id' => []
            ]
        ]);
    }

    /** @test */
    public function should_not_update_attribute_value_with_null_label_by_admin()
    {
        $attribute_value = factory(AttributeValue::class)->create();

        $attribute_value->label = null;
        $attribute_value->value = "new_value";
        $attribute_value = $attribute_value->toArray();

        unset($attribute_value['created_at']);
        unset($attribute_value['updated_at']);

        $response = $this->call('PUT', 'api/product-management/admin/attribute-value/' . $attribute_value['id'], $attribute_value);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'label' => []
            ]
        ]);
    }

    /** @test */
    public function can_delete_a_attribute_value()
    {
        $attribute_value = factory(AttributeValue::class)->create()->toArray();

        $response = $this->call('DELETE', 'api/product-management/admin/attribute-value/' . $attribute_value['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function should_not_create_existed_attribute_value_by_admin() {
        
        $existed_attribute_value = factory(AttributeValue::class)->create()->toArray();

        $response = $this->call('POST', 'api/product-management/admin/attribute-value', $existed_attribute_value);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Giá trị của thuộc tính này đã tồn tại !']);
    }

    /** @test */
    public function should_not_update_existed_attribute_value_by_admin() {
        
        $existed_attribute_values = factory(AttributeValue::class, 2)->create()->toArray();

        $existed_attribute_values[1]['attribute_id'] = $existed_attribute_values[0]['attribute_id'];
        $existed_attribute_values[1]['label'] = $existed_attribute_values[0]['label'];

        $response = $this->call('PUT', 'api/product-management/admin/attribute-value/'.$existed_attribute_values[1]['id'], $existed_attribute_values[1]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Giá trị của thuộc tính này đã tồn tại !']);
    }
}
