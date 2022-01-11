<?php

namespace VCComponent\Laravel\Product\Traits;

trait ProductQueryTrait
{
    
    /**
     * Scope a query to only include products of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('product_type', $type);
    }

    /**
     * Get product collection by type
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByType($type = 'products')
    {
        return self::ofType($type)->get();
    }

    /**
     * Get product by type with pagination
     *
     * @param string $type
     * @param int $per_page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getByTypeWithPagination($type = 'products', $per_page = 15)
    {
        return self::ofType($type)->paginate($per_page);
    }

    /**
     * Get product by type and id
     *
     * @param string $type
     * @param int $id
     * @return self
     */
    public static function findByType($id, $type = 'products')
    {
        try {
            return self::ofType($type)->where('id', $id)->firstOrFail();
        } catch (Exception $e) {
            throw new NotFoundException(Str::title($type));
        }
    }

    /**
     * Get product meta data
     *
     * @param string $key
     * @return string
     */
    public function getMetaField($key)
    {
        if (!$this->productMetas->count()) {
            throw new NotFoundException($key . ' field');
        }

        try {
            return $this->productMetas->where('key', $key)->first()->value;
        } catch (Exception $e) {
            throw new NotFoundException($key . ' field');
        }
    }

    /**
     * Scope a query to only include hot products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsHot($query)
    {
        return $query->where('is_hot', 1);
    }

    /**
     * Scope a query to only include in stock products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInStock($query)
    {
        return $query->where('quality' ,'>' , 0);
    }

    /**
     * Scope a query to only include publisded products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPublished($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to sort products by order column.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByOrder($query, $order = 'desc')
    {
        return $query->orderBy('order', $order);
    }

    /**
     * Scope a query to sort products by published_date column.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByPublishedDate($query, $order = 'desc')
    {
        return $query->orderBy('published_date', $order);
    }

    /**
     * Scope a query to sort products by sold quanlity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortBySoldQuanlity($query, $order = 'desc')
    {
        $query = $query->orderBy('sold_quantity', $order);
    }

    /**
     * Scope a query to search products of given key word. This function is also able to scope with categories, or tags.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $search
     * @param boolean $with_category
     * @param boolean $with_tag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSearching($query, $search, $with_category = false, $with_tag = false)
    {
        $query = $query->where(function ($q) use ($search) {
            $q->orWhere('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
        });

        if ($with_category && method_exists($this, 'categories')) {
            $query->whereHas('categories', function ($q) use ($search) {
                $q->whereIn('name', 'like', "%{$search}%")->where('status', 1);
            });
        }

        if ($with_tag && method_exists($this, 'tags')) {
            $query->whereHas('tags', function ($q) use ($search) {
                $q->whereIn('name', 'like', "%{$search}%")->where('status', 1);
            });
        }

        return $query;
    }

    /**
     * Scope a query to include related products. This function is also able to scope with categories, or tags.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param \VCComponent\Laravel\Product\Entities\Product $product
     * @param boolean $with_category
     * @param boolean $with_tag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfRelatingTo($query, $product, $with_category = false, $with_tag = false)
    {
        if ($product) {
            $query = $query->where('id', '<>', $product->id);

            if ($with_category && count($product->categories)) {
                $query = $query->ofCategoriesBySlug($product->categories->pluck('slug')->toArray());
            }

            if ($with_tag && count($product->tags)) {
                $query = $query->ofTagsBySlug($product->tags->pluck('slug')->toArray());
            }
        }
        return $query;
    }
}
