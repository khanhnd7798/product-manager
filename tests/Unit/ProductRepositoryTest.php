<?php

namespace VCComponent\Laravel\Product\Test\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Test\TestCase;
use Illuminate\Support\Facades\App;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Entities\Categoryable;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Repositories\ProductRepositoryEloquent;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;


    /**
     * @test
     */

    public function can_get_product_url()
    {
        $repository = App::make(ProductRepository::class);
        $product_a  = factory(Product::class)->create(['name'=>'a']);
        $product_b  = factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('/products/'.$product_a->slug, $repository->getProductUrl($product_a->id));
        $this->assertSame('/products/'.$product_b->slug, $repository->getProductUrl($product_b->id));
    }

    /**
     * @test
     */

    public function can_get_product_by_id()
    {
        $repository = App::make(ProductRepository::class);
        $product_a  = factory(Product::class)->create(['name'=>'a']);
        $product_b  = factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->getProductByID($product_a->id)->name);
        $this->assertSame('b', $repository->getProductByID($product_b->id)->name);
    }

    /**
     * @test
     */

    public function can_find_by_where()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a']);
        factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->findByWhere(['name'=>'a'])[0]->name);
    }

    /**
     * @test
     */

    public function can_find_by_where_paginate()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a']);
        factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->findByWherePaginate(['name'=>'a'])[0]->name);
    }


    /**
     * @test
     */

    public function can_find_product_by_field()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a']);
        factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->findProductByField('name','a')[0]->name);
    }

    /**
     * @test
     */

    public function can_get_search_result_paginate()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a test function result','description'=>'test']);
        factory(Product::class)->create(['name'=>'b test function','description'=>'test']);
        factory(Product::class)->create(['name'=>'c test function','description'=>'result']);
        $this->assertSame('a test function result', $repository->getSearchResultPaginate('result',['name','description'])[0]->name);
        $this->assertSame('c test function', $repository->getSearchResultPaginate('result',['name','description'])[1]->name);
    }

     /**
     * @test
     */

    public function can_get_search_result()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a test function result','description'=>'test']);
        factory(Product::class)->create(['name'=>'b test function','description'=>'test']);
        factory(Product::class)->create(['name'=>'c test function','description'=>'result']);
        $this->assertSame('a test function result', $repository->getSearchResult('result',['name','description'])[0]->name);
        $this->assertSame('c test function', $repository->getSearchResult('result',['name','description'])[1]->name);
    }

    /**
     * @test
     */

    public function can_get_products_with_category_paginate()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>3,'categoryable_type' => 'products']);
        $this->assertSame('a test function', $repository->getProductsWithCategoryPaginate(1)[0]->name);
        $this->assertSame('c test function', $repository->getProductsWithCategoryPaginate(2)[0]->name);
    }
    /**
     * @test
     */
    public function can_get_products_with_category()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>3,'categoryable_type' => 'products']);
        $this->assertSame('a test function', $repository->getProductsWithCategory(1)[0]->name);
        $this->assertSame('c test function', $repository->getProductsWithCategory(2)[0]->name);
    }

    /**
     * @test
     */
    public function can_get_related_products_paginate()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        factory(Product::class)->create(['name'=>'d test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>3,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>4,'categoryable_type' => 'products']);
        $this->assertSame('b test function', $repository->getRelatedProductsPaginate(1)[0]->name);
        $this->assertSame('c test function', $repository->getRelatedProductsPaginate(1)[1]->name);
    }

    /**
     * @test
     */
    
    public function can_get_related_products()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        factory(Product::class)->create(['name'=>'d test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>3,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>4,'categoryable_type' => 'products']);
        $this->assertSame('b test function', $repository->getRelatedProducts(1)[0]->name);
        $this->assertSame('c test function', $repository->getRelatedProducts(1)[1]->name);
    }


    // Add test from here

    /**
     * @test
     */

    public function can_get_list_hot_products()
    {
        $product_repository = App::make(ProductRepositoryEloquent::class);
        $data_products = factory(Product::class, 3)->create(['is_hot' => 1, 'product_type' => 'products'])->sortByDesc('created_at')->sortBy('order');
        $type = 'products';
        $products = $product_repository->getListHotProducts(3, $type);
        $this->assertProductsEqualDatas($products, $data_products);
    }

    /**
     * @test
     */

    public function can_get_list_related_hot_products()
    {
        $product_repository = App::make(ProductRepositoryEloquent::class);
        $data_products = factory(Product::class, 3)->create(['is_hot' => 1, 'product_type' => 'products'])->sortByDesc('created_at')->sortBy('order');
        $type = 'products';
        $product = factory(Product::class)->create();
        $products = $product_repository->getListRelatedHotProducts($product, 6, $type);
        $this->assertProductsEqualDatas($products, $data_products);
    }

    /**
     * @test
     */

    public function can_get_list_paginated_hot_products()
    {
        $product_repository = App::make(ProductRepositoryEloquent::class);
        $data_products = factory(Product::class, 3)->create(['is_hot' => 1, 'product_type' => 'products'])->sortByDesc('created_at')->sortBy('order');
        $type = 'products';
        $products = $product_repository->getListPaginatedHotProducts(3, $type);
        $this->assertProductsEqualDatas($products, $data_products);

    }

    /**
     * @test
     */

    public function can_get_list_paginated_related_products_by_repository_function()
    {
        $product_repository = app(ProductRepositoryEloquent::class);
        $related_products = factory(Product::class, 3)->create(['product_type' => 'products'])->sortBy('name')->sortByDesc('created_at')->sortBy('order');
        $product = factory(Product::class)->create();
        $type = 'products';
        $products = $product_repository->getListPaginatedRelatedProducts($product , 15, $type);
        $this->assertProductsEqualDatas($products, $related_products);
    }

    
    /**
     * @test
     */

    public function can_get_list_of_searching_products(){
        $product_repository = App::make(ProductRepositoryEloquent::class);
        $of_searching_products = factory(Product::class, 3)->create([
            'name' => 'searching_name',
            'product_type' => 'products'
        ])->sortByDesc('created_at')->sortBy('order');
        $products = $product_repository->getListOfSearchingProducts('searching_name');
        $this->assertProductsEqualDatas($products, $of_searching_products);
    }

    /**
     * @test
     */

    public function can_get_list_paginated_searching_products(){
        $product_repository = App::make(ProductRepositoryEloquent::class);
        $of_searching_products = factory(Product::class, 3)->create([
            'name' => 'searching_name',
            'product_type' => 'products'
        ])->sortByDesc('created_at')->sortBy('order');
        $products = $product_repository->getListPaginatedOfSearchingProducts('searching_name');
        $this->assertTrue($products instanceof LengthAwarePaginator);
        $this->assertProductsEqualDatas($products, $of_searching_products);
    }


    protected function assertProductsEqualDatas($products, $data_products) {
        $this->assertEquals($products->pluck('name'), $data_products->pluck('name'));
        $this->assertEquals($products->pluck('description'), $data_products->pluck('description'));
        $this->assertEquals($products->pluck('order'), $data_products->pluck('order'));
        $this->assertEquals($products->pluck('product_type'), $data_products->pluck('product_type'));
    }

}
