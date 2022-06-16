<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProductRepository.
 *
 * @package namespace VCComponent\Laravel\Product\Repositories;
 */
interface ProductRepository extends RepositoryInterface
{
    public function getWithPagination($filters, $type);
    public function getStock($query);
    public function getMaxId();
    public function getOutStock($query);
    public function bulkUpdateStatus($request);

    public function getRelatedProducts($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc');
    public function getRelatedProductsPaginate($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc');
    public function getProductsWithCategory($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*']);
    public function getProductsWithCategoryPaginate($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*']);
    public function getSearchResult($key_word,array $list_field  = ['name'], array $where = [], $category_id = 0,$number = 10,$order_by = 'order', $order = 'asc', $columns = ['*']);
    public function getSearchResultPaginate($key_word, array $list_field  = ['name'], array $where = [], $category_id = 0,$number = 10,$order_by = 'order', $order = 'asc', $columns = ['*']);
    public function findProductByField($field, $value);
    public function findByWherePaginate(array $where, $number = 10, $order_by = 'order', $order = 'asc');
    public function findByWhere(array $where, $number = 10, $order_by = 'order', $order = 'asc');
    public function getProductByID($product_id);
    public function getProductMedias($product_id, $image_dimension= '');
    public function getProductUrl($product_id);

    public function getListHotProducts($number = null, $type = 'products');
    public function getListRelatedHotProducts($product, $number = null);

    public function getListPaginatedHotProducts($per_page = 15, $type = 'products');
    public function getListPaginatedRelatedProducts($product, $per_page = 15);

    public function getListOfSearchingProducts($search, $number = null, $type = 'products', $absolute_search = false);
    public function getListPaginatedOfSearchingProducts($search, $per_page = 15, $type = 'products', $absolute_search = false);

    public function getListHotTranslatableProducts($number = null, $type = 'products');
    public function getListRelatedTranslatableProducts($product, $number = null);

    public function getListOfSearchingTranslatableProducts($search, $number = null, $type = 'products', $absolute_search = false);
    public function getListPaginatedHotTranslatableProducts($per_page = 15, $type = 'products');
    
    public function getListPaginatedRelatedTranslatableProducts($product, $per_page = 15);
    public function getListPaginatedOfSearchingTranslatableProducts($search, $per_page = 15, $type = 'products', $absolute_search = false);
}
