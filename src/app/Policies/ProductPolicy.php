<?php

namespace VCComponent\Laravel\Product\Policies;

use VCComponent\Laravel\Product\Contracts\ProductPolicyInterface;

class ProductPolicy implements ProductPolicyInterface
{
    public function ableToUse($user)
    {
        return true;
    }

    public function ableToShow($user, $model)
    {
        return true;
    }

    public function ableToCreate($user)
    {
        return true;
    }

    public function ableToUpdate($user)
    {
        return true;
    }

    public function ableToUpdateItem($user, $model)
    {
        return true;
    }

    public function ableToDelete($user, $model)
    {
        return true;
    }
}