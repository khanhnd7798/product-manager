<?php

namespace VCComponent\Laravel\Product\Contracts;

interface ProductPolicyInterface
{
    public function ableToUse($user);
    public function ableToShow($user, $model);
    public function ableToCreate($user);
    public function ableToUpdateItem($user, $model);
    public function ableToUpdate($user);
    public function ableToDelete($user, $model);
}