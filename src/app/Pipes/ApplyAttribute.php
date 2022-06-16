<?php

namespace VCComponent\Laravel\Product\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Contracts\Pipe;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Entities\ProductAttribute;

class ApplyAttribute implements Pipe
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($content, Closure $next)
    {
        $attributes = [];
        if(!is_array($this->request->get('attributes'))){
            $attributes[] = $this->request->get('attributes');
        }
        if ($this->request->has('attributes')) {
            $content = $content->whereHas('attributesValue.attributeItem', function (Builder $query) use ($attributes) {
                $query->whereIn('value', $attributes);
            });
        }
        return $next($content);
    }
}
