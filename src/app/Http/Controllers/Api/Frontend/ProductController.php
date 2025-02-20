<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use VCComponent\Laravel\Product\Traits\Helpers;
use VCComponent\Laravel\Product\Events\ProductCreatedEvent;
use VCComponent\Laravel\Product\Events\ProductDeletedEvent;
use VCComponent\Laravel\Product\Events\ProductUpdatedEvent;
use VCComponent\Laravel\Product\Validators\ProductValidator;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Transformers\ProductTransformer;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;
use VCComponent\Laravel\Vicoders\Core\Exceptions\PermissionDeniedException;

class ProductController extends ApiController
{
    use Helpers;

    public function __construct(ProductRepository $repository, ProductValidator $validator, Request $request)
    {
        $this->repository = $repository;
        $this->entity = $repository->getEntity();
        $this->validator = $validator;

        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('manage', $this->entity)) {
                throw new PermissionDeniedException();
            }

            foreach(config('product.auth_middleware.frontend') as $middleware){
                $this->middleware($middleware['middleware'], ['except' => $middleware['except']]);
            }
        }

        if (isset(config('product.transformers')['product'])) {
            $this->transformer = config('product.transformers.product');
        } else {
            $this->transformer = ProductTransformer::class;
        }
    }

    public function index(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request, ['productMetas' => ['value']]);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $products = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($products, $transformer);
    }

    function list(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request, ['productMetas' => ['value']]);
        $query = $this->applyOrderByFromRequest($query, $request);

        $products = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->collection($products, $transformer);
    }

    public function show(Request $request, $id)
    {
        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('view', $product)) {
                throw new PermissionDeniedException();
            }
        }

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->item($product, $transformer);
    }

    public function store(Request $request)
    {
        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('create', $this->entity)) {
                throw new PermissionDeniedException();
            }
        }
        $data = $this->filterProductRequestData($request, $this->entity);
        $schema_rules = $this->validator->getSchemaRules($this->entity);
        $no_rule_fields = $this->validator->getNoRuleFields($this->entity);

        $this->validator->isValid($data['default'], 'RULE_ADMIN_CREATE');
        $this->validator->isSchemaValid($data['schema'], $schema_rules);

        $product = $this->repository->create($data['default']);
        $product->save();

        if (count($data['schema'])) {
            foreach ($data['schema'] as $key => $value) {
                $product->productMetas()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        if (count($no_rule_fields)) {
            foreach ($no_rule_fields as $key => $value) {
                $product->productMetas()->updateOrCreate([
                    'key' => $key,
                    'value' => null,
                ], ['value' => '']);
            }
        }

        $this->addAttributes($request, $product);

        event(new ProductCreatedEvent($product));

        return $this->response->item($product, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('update-item-product', $product)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $this->filterProductRequestData($request, $this->entity);
        $schema_rules = $this->validator->getSchemaRules($this->entity);

        $this->validator->isValid($data['default'], 'RULE_ADMIN_UPDATE');
        $this->validator->isSchemaValid($data['schema'], $schema_rules);

        $product = $this->repository->update($data['default'], $id);

        if ($request->has('status')) {
            $product->status = $request->get('status');
            $product->save();
        }

        if (count($data['schema'])) {
            foreach ($data['schema'] as $key => $value) {
                $product->productMetas()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        $this->updateAttributes($request, $product);

        event(new ProductUpdatedEvent($product));


        return $this->response->item($product, new $this->transformer);
    }

    public function destroy(Request $request, $id)
    {
        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('delete', $product)) {
                throw new PermissionDeniedException();
            }
        }

        $this->repository->delete($id);
        $this->deleteAttributes($id);

        event(new ProductDeletedEvent($product));

        return $this->success();
    }


    public function bulkUpdateStatus(Request $request)
    {
        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('update', $this->entity)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $request->all();

        $products = $this->entity->whereIn('id', $data['ids'])
            ->get();

        if ($products->count() == 0) {
            throw new NotFoundException('Products');
        }

        $this->validator->isValid($request, 'BULK_UPDATE_STATUS');

        foreach ($products as $product) {
            $product->status = $data['status'];
            $product->save();
        }

        return $this->success();
    }

    public function updateStatusItem(Request $request, $id)
    {
        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (!empty(config('product.auth_middleware.frontend'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('update-item', $product)) {
                throw new PermissionDeniedException();
            }
        }

        $this->validator->isValid($request, 'UPDATE_STATUS_ITEM');

        $data         = $request->all();
        $product->status = $data['status'];
        $product->save();

        return $this->success();
    }
}
