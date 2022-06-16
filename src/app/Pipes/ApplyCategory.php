<?php

namespace VCComponent\Laravel\Product\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Contracts\Pipe;

class ApplyCategory implements Pipe
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($content, Closure $next)
    {
        $categories = [];
        if(!is_array($this->request->get('categories'))){
            $categories[] = $this->request->get('categories');
        }
        if($this->request->has('categories')){
            $content = $content->whereHas('categories', function(Builder $query)  use ($categories){
                $query->whereIn('slug', $categories);
            });
        }
        
        return $next($content);
    }
}
