<?php

namespace VCComponent\Laravel\Product\Repositories;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;
use VCComponent\Laravel\Category\Entities\Categoryable;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace VCComponent\Laravel\Product\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        if (isset(config('product.models')['product'])) {
            return config('product.models.product');
        } else {
            return Product::class;
        }
    }

    public function getEntity()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getWithPagination($filters, $type)
    {
        $request = App::make(Request::class);
        $query   = $this->getEntity();

        $items = App::make(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->then(function ($product_type) use ($request, $type) {
                $product_type  = $product_type->where('product_type', $type);
                $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
                $products = $product_type->paginate($per_page);
                return $products;
            });

        return $items;
    }

    public function getMaxId()
    {
        $query = $this->getEntity();

        $max_id = $query->max('id');

        return ($max_id);
    }

    public function getStock($query)
    {
        $stock = $query->where('quantity', '>', 0);
        return $stock;
    }

    public function getOutStock($query)
    {
        $out_stock = $query->where('quantity', '<=', 0);
        return $out_stock;
    }

    public function checkSku($sku)
    {
        $check_sku = $this->getEntity()->where('sku', $sku)->get();
        return count($check_sku);
    }

    public function bulkUpdateStatus($request)
    {

        $data  = $request->all();
        $products = $this->model->whereIn("id", $request->ids)->get();

        if (count($request->ids) > $products->count()) {
            throw new NotFoundException("Products");
        }

        $result = $this->model->whereIn("id", $request->ids)->update(['status' => $data['status']]);

        return $result;
    }

    public function restore($id)
    {

        $product = $this->model->where('id', $id)->restore();
    }

    public function bulkRestore($ids)
    {

        $products = $this->model->whereIn("id", $ids)->restore();
    }

    public function deleteTrash($id)
    {
        $product = $this->model->where("id", $id)->forceDelete();
    }

    public function forceDelete($id)
    {

        $product = $this->model->where("id", $id)->forceDelete();
    }

    public function bulkDeleteTrash($ids)
    {

        $products = $this->model->whereIn('id', $ids)->forceDelete();
    }

    public function getRelatedProducts($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc')
    {
        $categories = Categoryable::where('categoryable_id', $product_id)->where('categoryable_type', 'products')->first();
        $query = Categoryable::where('category_id', $categories->category_id)
            ->join('products', 'categoryable_id', '=', 'products.id')->select('products.*')
            ->where('categoryable_type', 'products')
            ->where('products.id', '<>', $product_id)
            ->where($where)
            ->orderBy($order_by, $order);

        if ($number > 0) {
            return $query->limit($number)->get();
        }
        return $query->get();
    }
    public function getRelatedProductsPaginate($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc')
    {
        $categories = Categoryable::where('categoryable_id', $product_id)->where('categoryable_type', 'products')->first();
        $query = Categoryable::where('category_id', $categories->category_id)
            ->join('products', 'categoryable_id', '=', 'products.id')->select('products.*')
            ->where('categoryable_type', 'products')
            ->where('products.id', '<>', $product_id)
            ->where($where)
            ->orderBy($order_by, $order);
        return $query->paginate($number);
    }

    public function getProductsWithCategory($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*'])
    {
        $query = $this->getEntity()->where($where)
            ->orderBy($order_by, $order);
        $query = $query->whereHas('categories', function ($q) use ($category_id) {
            $q->where('categories.id', $category_id);
        });
        if ($number > 0) {
            return $query->limit($number)->get($columns);
        }
        return $query->get($columns);
    }

    public function getProductsWithCategoryPaginate($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*'])
    {
        $query = $this->getEntity()->select($columns)
            ->where($where)
            ->orderBy($order_by, $order);
        $query = $query->whereHas('categories', function ($q) use ($category_id) {
            $q->where('categories.id', $category_id);
        });
        return $query->paginate($number);
    }

    public function getSearchResult($key_word, array $list_field = ['name'], array $where = [], $category_id = 0, $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*'])
    {
        $query = $this->getEntity()->where(function ($q) use ($list_field, $key_word) {
            foreach ($list_field  as $field)
                $q->orWhere($field, 'like', "%{$key_word}%");
        });
        $query->where($where)
            ->orderBy($order_by, $order);
        if ($category_id > 0) {
            $query = $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        if ($number > 0) {
            return $query->limit($number)->get($columns);
        }
        return $query->get($columns);
    }
    public function getSearchResultPaginate($key_word, array $list_field  = ['name'], array $where = [], $category_id = 0, $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*'])
    {
        $query = $this->getEntity()->where(function ($q) use ($list_field, $key_word) {
            foreach ($list_field  as $field)
                $q->orWhere($field, 'like', "%{$key_word}%");
        });
        $query->select($columns)->where($where)
            ->orderBy($order_by, $order);
        if ($category_id > 0) {
            $query = $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }
        return $query->paginate($number);
    }

    public function findProductByField($field, $value)
    {
        return $this->getEntity()->where($field, '=', $value)->get();
    }
    public function findByWhere(array $where, $number = 10, $order_by = 'order', $order = 'asc')
    {
        $query = $this->getEntity()->where($where)->orderBy($order_by, $order);
        if ($number > 0) {
            return $query->limit($number)->get();
        }
        return $query->get();
    }
    
    public function findByWherePaginate(array $where, $number = 10, $order_by = 'order', $order = 'asc')
    {
        return $this->getEntity()->where($where)->orderBy($order_by, $order)->paginate($number);
    }

    public function getProductByID($product_id)
    {
        return $this->getEntity()->find($product_id);
    }

    public function getProductMedias($product_id, $image_dimension = '')
    {
        $product = $this->getEntity()->where('id', $product_id)->first();
        $images = [];
        $count = 0;
        foreach ($product->getMedia() as $item) {
            $images[$count] = $item->getUrl($image_dimension);
            $count++;
        }
        return $images;
    }

    public function getProductUrl($product_id)
    {
        $product = $this->getEntity()->find($product_id);
        return '/products' . '/' . $product->slug;
    }

    // Add function from here

    public function getListHotProducts($number = null, $type = 'products')
    {
        $query = $this->getEntity()->where('product_type', $type)
            ->where('is_hot', 1)
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->latest();
        $query = $number ? $query->limit($number) : $query;
        return $query->get();
    }

    public function getListRelatedHotProducts($product, $number = null, $type = 'products')
    {
        $query = $this->getEntity()->where('product_type', $type)
            ->where('id', '<>', $product->id)
            ->where('status', 1)
            ->where('is_hot', 1)
            ->orderBy('order', 'desc')
            ->latest();
        $query = $number ? $query->limit($number) : $query;
        return $query->get();
    }

    
    public function getListPaginatedHotProducts($per_page = 15, $type = 'products')
    {
        $query = $this->getEntity()->where('product_type', $type)
            ->where('is_hot', 1)
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->latest();
        return $query->paginate($per_page);
    }

    
    public function getListPaginatedRelatedProducts($product, $per_page = 15, $type = 'products')
    {
        $query = $this->getEntity()->where('product_type', $type)
            ->where('id', '<>', $product->id)
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->latest();
        return $query->paginate($per_page);
    }

    public function getListOfSearchingProducts($search, $number = null, $type = 'products', $absolute_search = false)
    {
        if (!$absolute_search) {
            $search = '%' . $search . '%';
        }
        $query = $this->getEntity()->where('product_type', $type)
            ->where(function ($where_query) use ($search) {
                $where_query
                    ->orWhere('name', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('product_type', 'like', $search);
            })
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->latest();
        $query = $number ? $query->limit($number) : $query;
        return $query->get();
    }

    public function getListPaginatedOfSearchingProducts($search, $per_page = 15, $type = 'products', $absolute_search = false)
    {
        if (!$absolute_search) {
            $search = '%' . $search . '%';
        }

        $query = $this->getEntity()->where('product_type', $type)
            ->where(function ($where_query) use ($search) {
                $where_query
                ->orWhere('name', 'like', $search)
                ->orWhere('description', 'like', $search)
                ->orWhere('product_type', 'like', $search);
            })
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->latest();
        return $query->paginate($per_page);
    }

    public function getListHotTranslatableProducts($number = null, $type = 'product')
    {
        $query = $this->getEntity()->where('product_type', $type)
            ->with('languages')
            ->with('productMetas')
            ->where('is_hot', 1)
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->latest();
        $query = $number ? $query->limit($number) : $query;
        return $query->get();
    }

    public function getListRelatedTransalatableProducts($product, $number = null)
    {
        $query = $this->getEntity()->ofType($product->type)
            ->with('languages')
            ->with('productMetas')
            ->with('categories')
            ->with('tags')
            ->where('id', '<>', $product->id)
            ->where('status', 1)
            ->orderBy('order', 'asc')
            ->latest();
        $query = $number ? $query->limit($number) : $query;
        return $query->get();
    }

    public function getListOfSearchingTranslatableProducts($search, $number = null, $type = 'products', $absolute_search = false)
    {
        if (!$absolute_search) {
            $search = '%' . $search . '%';
        }

        $query = $this->getEntity()->where('product_type', $type)->with('languages')
            ->where(function ($where_query) use ($search) {
                $where_query
                ->orWhere('name', 'like', $search)
                ->orWhere('description', 'like', $search)
                ->orWhere('product_type', 'like', $search);
            })
            ->with('productMetas')
            ->with('categories')
            ->with('tags')
            ->where('status', 1)
            ->orderBy('order', 'asc')
            ->latest();
        $query = $number ? $query->limit($number) : $query;
        return $query->get();
    }

    public function getListPaginatedHotTranslatableProducts($per_page = 15, $type = 'products')
    {
        $query = $this->getEntity()->where('product_type', $type)->with('languages')
            ->with('productMetas')
            ->with('categories')
            ->with('tags')
            ->where('is_hot', 1)
            ->where('status', 1)
            ->orderBy('order', 'asc')
            ->latest();
        return $query->paginate($per_page);
    }

    public function getListPaginatedRelatedTranslatableProducts($product, $per_page = 15)
    {
        $query = $this->getEntity()->ofType($product->type)
            ->with('languages')
            ->where('id', '<>', $product->id)
            ->where('status', 1)
            ->orderBy('order', 'asc')
            ->latest();
        return $query->paginate($per_page);
    }

    public function getListPaginatedOfSearchingTranslatableProducts($search, $per_page = 15, $type = 'products', $absolute_search = false)
    {
        if (!$absolute_search) {
            $search = '%' . $search . '%';
        }

        $query = $this->getEntity()->where('product_type', $type)->with('languages')
            ->where(function ($where_query) use ($search) {
                $where_query
                ->orWhere('name', 'like', $search)
                ->orWhere('description', 'like', $search)
                ->orWhere('product_type', 'like', $search);
            })
            ->where('status', 1)
            ->orderBy('order', 'asc')
            ->latest();
        return $query->paginate($per_page);
    }
}
