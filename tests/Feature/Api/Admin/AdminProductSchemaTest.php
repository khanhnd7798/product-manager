<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchema;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchemaRule;
use VCComponent\Laravel\Product\Test\Stubs\Models\ProductSchemaType;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminProductSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_paginated_product_schemas_by_admin()
    {
        $product_schemas = factory(ProductSchema::class, 5)->create();

        $product_schemas = $product_schemas->map(function ($ps) {
            unset($ps['created_at']);
            unset($ps['updated_at']);
            return $ps;
        })->toArray();

        $listIds = array_column($product_schemas, 'id');
        array_multisort($listIds, SORT_DESC, $product_schemas);

        $response = $this->call('GET', 'api/product-management/admin/schemas');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $product_schemas
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

    /** @test */
    public function can_get_paginated_product_schemas_with_constraints_by_admin()
    {
        $product_schemas = factory(ProductSchema::class, 5)->create();

        $product_schemas = $product_schemas->map(function ($ps) {
            unset($ps['created_at']);
            unset($ps['updated_at']);
            return $ps;
        })->toArray();

        $listIds = array_column($product_schemas, 'id');
        array_multisort($listIds, SORT_DESC, $product_schemas);

        $constraints = '{"name":"' . $product_schemas[0]['name'] . '"}';

        $response = $this->call('GET', 'api/product-management/admin/schemas?consrtaints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$product_schemas[0]]
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

    /** @test */
    public function can_get_paginated_product_schemas_with_search_by_admin()
    {
        $product_schemas = factory(ProductSchema::class, 5)->create();

        $search = $product_schemas[0]['name'];

        $product_schemas = DB::table('product_schemas')->where('name', 'like', $search)->get();
        $product_schemas = $product_schemas->map(function ($ps) {
            $ps = (array) $ps;
            unset($ps['created_at']);
            unset($ps['updated_at']);
            return $ps;
        })->toArray();

        $listIds = array_column($product_schemas, 'id');
        array_multisort($listIds, SORT_DESC, $product_schemas);

        $response = $this->call('GET', 'api/product-management/admin/schemas?search=' . $search);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $product_schemas
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

    /** @test */
    public function can_get_paginated_product_schemas_with_order_by_by_admin()
    {
        $product_schemas = factory(ProductSchema::class, 5)->create();

        $product_schemas = $product_schemas->map(function ($ps) {
            unset($ps['created_at']);
            unset($ps['updated_at']);
            return $ps;
        })->toArray();

        $listIds = array_column($product_schemas, 'id');
        array_multisort($listIds, SORT_DESC, $product_schemas);

        $listNames = array_column($product_schemas, 'name');
        array_multisort($listNames, SORT_DESC, $product_schemas);

        $order_by = '{"name":"desc"}';

        $response = $this->call('GET', 'api/product-management/admin/schemas?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$product_schemas[0]]
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

    /** @test */
    public function can_get_a_product_schema_by_admin()
    {
        $product_schema = factory(ProductSchema::class)->create()->toArray();

        unset($product_schema['created_at']);
        unset($product_schema['updated_at']);

        $response = $this->call('GET', 'api/product-management/admin/schemas/' . $product_schema['id']);

        $response->assertStatus(200);
        $response->assertjson(['data' => $product_schema]);
    }

    /** @test */
    public function should_not_get_product_chema_with_undefined_id_by_admin()
    {
        $response = $this->call('GET', 'api/product-management/admin/schemas/undefine-ids');

        $response->assertStatus(500);
        $response->assertjson(['message' => 'Không tìm thấy thuộc tính !']);
    }

    /** @test */
    public function can_create_a_product_schema_by_admin()
    {
        $data = factory(ProductSchema::class)->make()->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/schemas', $data);

        $response->assertStatus(200);
        $response->assertjson(['data' => $data]);
    }

    /** @test */
    public function can_create_a_product_schema_without_label_by_admin()
    {
        $data = factory(ProductSchema::class)->make([
            'label' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/schemas', $data);

        $response->assertStatus(422);
        $response->assertjson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'label' => []
            ]
        ]);
    }

    /** @test */
    public function can_create_a_product_schema_without_schema_rule_id_by_admin()
    {
        $data = factory(ProductSchema::class)->make([
            'schema_rule_id' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/schemas', $data);

        $response->assertStatus(422);
        $response->assertjson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'schema_rule_id' => []
            ]
        ]);
    }

    /** @test */
    public function can_create_a_product_schema_without_schema_type_id_by_admin()
    {
        $data = factory(ProductSchema::class)->make([
            'schema_type_id' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/schemas', $data);

        $response->assertStatus(422);
        $response->assertjson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'schema_type_id' => []
            ]
        ]);
    }

    /** @test */
    public function can_create_a_product_schema_without_product_type_by_admin()
    {
        $data = factory(ProductSchema::class)->make([
            'product_type' => null
        ])->toArray();

        unset($data['created_at']);
        unset($data['updated_at']);

        $response = $this->call('POST', 'api/product-management/admin/schemas', $data);

        $response->assertStatus(422);
        $response->assertjson(['message' => 'The given data was invalid.']);
        $response->assertJsonStructure([
            'errors' => [
                'product_type' => []
            ]
        ]);
    }

    /** @test */
    public function can_update_a_product_schema_by_admin()
    {
        $product_schema = factory(ProductSchema::class)->create();
        $product_schema->name = "new_name";
        $product_schema->label = "new_label";
        $product_schema->schema_rule_id = rand(1, 5);
        $product_schema->schema_type_id = rand(1, 5);

        unset($product_schema['updated_at']);
        unset($product_schema['created_at']);

        $product_schema = $product_schema->toArray();

        $response = $this->call('PUT', 'api/product-management/admin/schemas/' . $product_schema['id'], $product_schema);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema]);
    }

    /** @test */
    public function should_not_update_a_product_schema_width_undefined_id_by_admin()
    {
        $product_schema = factory(ProductSchema::class)->make()->toArray();
        $response = $this->call('PUT', 'api/product-management/admin/schemas/' . rand(1, 5), $product_schema);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Không tìm thấy thuộc tính !']);
    }

    /** @test */
    public function can_delete_a_schema_by_admin()
    {
        $product_schema = factory(ProductSchema::class)->create()->toArray();

        $response =  $this->call('DELETE', 'api/product-management/admin/schemas/' . $product_schema['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDeleted('product_schemas', $product_schema);
    }

    /** @test */
    public function can_get_list_all_schema_rules_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $product_schema_rules = $product_schema_rules->map(function ($product_schema_rule) {
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule;
        })->toArray();

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules/all');

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
    }

    /** @test */
    public function can_get_list_all_schema_rules_with_constraints_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $constraint_name = $product_schema_rules[0]->name;

        $product_schema_rules = $product_schema_rules->filter(function ($product_schema_rule) use ($constraint_name) {
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule->name == $constraint_name;
        })->toArray();

        $constraints = '{"name":"' . $constraint_name . '"}';

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules/all?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
    }

    /** @test */
    public function can_get_list_all_schema_rules_order_by_with_search_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $search_name = $product_schema_rules[1]->name;
        
        $product_schema_rules = DB::table('product_schema_rules')->where('name', 'like', '%'.$search_name.'%')->get();
        $product_schema_rules = $product_schema_rules->map(function ($product_schema_rule) {
            $product_schema_rule = (array) $product_schema_rule;
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule;
        })->toArray();

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules/all?search=' . $search_name);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
    }

    /** @test */
    public function can_get_list_all_schema_rules_order_by_with_order_by_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $product_schema_rules = $product_schema_rules->map(function ($product_schema_rule) {
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule;
        })->toArray();

        $order_by = '{"name":"DESC"}';

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $listNames = array_column($product_schema_rules, 'name');
        array_multisort($listNames, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules/all?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
    }

    /** @test */
    public function can_get_list_paginate_schema_rules_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $product_schema_rules = $product_schema_rules->map(function ($product_schema_rule) {
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule;
        })->toArray();

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules');

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
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
    public function can_get_list_paginate_schema_rules_with_constraints_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $constraint_name = $product_schema_rules[0]->name;

        $product_schema_rules = $product_schema_rules->filter(function ($product_schema_rule) use ($constraint_name) {
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule->name == $constraint_name;
        })->toArray();

        $constraints = '{"name":"' . $constraint_name . '"}';

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
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
    public function can_get_list_paginate_schema_rules_order_by_with_search_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $search_name = $product_schema_rules[1]->name;

        $product_schema_rules = DB::table('product_schema_rules')->where('name', 'like', '%'.$search_name.'%')->get();
        $product_schema_rules = $product_schema_rules->map(function ($product_schema_rule) {
            $product_schema_rule = (array) $product_schema_rule;
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule;
        })->toArray();

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules?search=' . $search_name);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
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
    public function can_get_list_paginate_schema_rules_order_by_with_order_by_by_admin()
    {
        $product_schema_rules = factory(ProductSchemaRule::class, 5)->create();

        $product_schema_rules = $product_schema_rules->map(function ($product_schema_rule) {
            unset($product_schema_rule['created_at']);
            unset($product_schema_rule['updated_at']);
            return $product_schema_rule;
        })->toArray();

        $order_by = '{"name":"DESC"}';

        $listIds = array_column($product_schema_rules, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_rules);

        $listNames = array_column($product_schema_rules, 'name');
        array_multisort($listNames, SORT_DESC, $product_schema_rules);

        $response = $this->call('GET', 'api/product-management/admin/schema-rules?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_rules]);
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
    public function can_get_list_all_schema_types_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $product_schema_types = $product_schema_types->map(function ($product_schema_type) {
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type;
        })->toArray();

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types/all');

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
    }

    /** @test */
    public function can_get_list_all_schema_types_with_constraints_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $constraint_name = $product_schema_types[0]->name;

        $product_schema_types = $product_schema_types->filter(function ($product_schema_type) use ($constraint_name) {
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type->name == $constraint_name;
        })->toArray();

        $constraints = '{"name":"' . $constraint_name . '"}';

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types/all?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
    }

    /** @test */
    public function can_get_list_all_schema_types_order_by_with_search_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $search_name = $product_schema_types[1]->name;

        $product_schema_types = DB::table('product_schema_types')->where('name', 'like', '%'.$search_name.'%')->get();
        $product_schema_types = $product_schema_types->map(function ($product_schema_type) {
            $product_schema_type = (array) $product_schema_type;
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type;
        })->toArray();

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types/all?search=' . $search_name);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
    }

    /** @test */
    public function can_get_list_all_schema_types_order_by_with_order_by_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 2)->create();

        $product_schema_types = $product_schema_types->map(function ($product_schema_type) {
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type;
        })->toArray();

        $defaults = [
            ["id" => 1, "name" => "text"],
            ["id" => 2, "name" => "textarea"],
            ["id" => 3, "name" => "tinyMCE"],
            ["id" => 4, "name" => "checkbox"],
        ];

        $product_schema_types = array_merge($product_schema_types, $defaults);

        $order_by = '{"id":"ASC"}';

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_ASC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types/all?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
    }

    /** @test */
    public function can_get_list_paginate_schema_types_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $product_schema_types = $product_schema_types->map(function ($product_schema_type) {
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type;
        })->toArray();

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types');

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
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
    public function can_get_list_paginate_schema_types_with_constraints_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $constraint_name = $product_schema_types[0]->name;

        $product_schema_types = $product_schema_types->filter(function ($product_schema_type) use ($constraint_name) {
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type->name == $constraint_name;
        })->toArray();

        $constraints = '{"name":"' . $constraint_name . '"}';

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types?constraints=' . $constraints);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
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
    public function can_get_list_paginate_schema_types_order_by_with_search_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $search_name = $product_schema_types[1]->name;

        $product_schema_types = DB::table('product_schema_types')->where('name', 'like', '%'.$search_name.'%')->get();
        $product_schema_types = $product_schema_types->map(function ($product_schema_type) {
            $product_schema_type = (array) $product_schema_type;
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            unset($product_schema_type['deleted_at']);
            return $product_schema_type;
        })->toArray();

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types?search=' . $search_name);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
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
    public function can_get_list_paginate_schema_types_order_by_with_order_by_by_admin()
    {
        $product_schema_types = factory(ProductSchemaType::class, 5)->create();

        $product_schema_types = $product_schema_types->map(function ($product_schema_type) {
            unset($product_schema_type['created_at']);
            unset($product_schema_type['updated_at']);
            return $product_schema_type;
        })->toArray();

        $order_by = '{"name":"DESC"}';

        $response = $this->call('GET', 'api/product-management/admin/schema-types');
        $product_schema_types = $response->json()['data'];

        $listIds = array_column($product_schema_types, 'id');
        array_multisort($listIds, SORT_DESC, $product_schema_types);

        $listNames = array_column($product_schema_types, 'name');
        array_multisort($listNames, SORT_DESC, $product_schema_types);

        $response = $this->call('GET', 'api/product-management/admin/schema-types?order_by=' . $order_by);

        $response->assertStatus(200);
        $response->assertJson(['data' => $product_schema_types]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }
}
