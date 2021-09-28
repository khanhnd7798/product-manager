<?php

namespace VCComponent\Laravel\Product\Policies;

use VCComponent\Laravel\Product\Contracts\ProductPolicyInterface;

class ProductPolicy implements ProductPolicyInterface
{
    public function before($user, $ability)
    {
        if ($user->isAdministrator()) {
            return true;
        }
    }

    public function manage($user)
    {
        return $user->hasPermission('manage-product');
    }

    public function view($user, $model)
    {
        return $user->hasPermission('view-product');
    }

    public function create($user)
    {
        return $user->hasPermission('create-product');
    }

    public function update($user)
    {
        return $user->hasPermission('update-product');
    }

    public function updateItem($user, $model)
    {
        return $user->hasPermission('update-item-product');
    }

    public function delete($user, $model)
    {
        return $user->hasPermission('delete-product');
    }
}