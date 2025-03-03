<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Traits\Helpers;
use VCComponent\Laravel\Product\ViewModels\ProductDetail\ProductDetailViewModel;
use Illuminate\Support\Str;


class ProductDetailController extends Controller implements ViewProductDetailControllerInterface
{
    use Helpers;

    protected $repository;
    protected $entity;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();

        if (isset(config('post.viewModels')['productDetail'])) {
            $this->ViewModel = config('post.viewModels.productDetail');
        } else {
            $this->ViewModel = ProductDetailViewModel::class;
        }
    }

    public function show($slug, Request $request)
    {
        if (method_exists($this, 'beforeQuery')) {
            $this->beforeQuery($request);
        }

        $type    = $this->getTypeProduct($request);
        $product = $this->entity->where(['slug' => $slug, 'product_type' => $type, "status" => 1])->with('attributesValue')->firstOrFail();

        if (!$product) {
            return false;
        }

        if (method_exists($this, 'afterQuery')) {
            $this->afterQuery($product, $request);
        }

        $view_model = new $this->ViewModel($product);

        $custom_view_func_name = 'viewData' . ucwords(Str::camel($type));
        if (method_exists($this, $custom_view_func_name)) {
            $custom_view_data = $this->$custom_view_func_name($product, $request);
        } else {
            $custom_view_data = $this->viewData($product, $request);
        }

        $data = array_merge($view_model->toArray(), $custom_view_data);

        if (method_exists($this, 'beforeView')) {
            $this->beforeView($data, $request);
        }

        $key = 'view' . ucwords(Str::camel($type));

        if (method_exists($this, $key)) {
            return view($this->$key(), $data);
        } else {
            return view($this->view(), $data);
        }
    }

    protected function view()
    {
        return 'product-manager::product-detail';
    }

    protected function viewData($products, Request $request)
    {
        return [];
    }
}
