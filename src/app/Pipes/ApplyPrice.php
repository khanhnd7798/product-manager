<?php

namespace VCComponent\Laravel\Product\Pipes;

use Closure;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Contracts\Pipe;

class ApplyPrice implements Pipe{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($content, Closure $next)
    {
        $price_from = $this->request->get('price_from');
        $price_to = $this->request->get('price_to');

        if($this->request->has('price_from')){
            $content = $content->where('price', '>=', $price_from);
        }
        if($this->request->has('price_to')){
            $content = $content->where('price', '<=', $price_to);
        }

        return $next($content);
    }
}