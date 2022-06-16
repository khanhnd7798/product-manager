<?php

namespace VCComponent\Laravel\Product\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait ProductHelper
{
    /**
     * Get the user record associated with the product.
     */
    public function author()
    {
        if (config('auth.providers.users.model')) {
            return $this->belongsTo(config('auth.providers.users.model'));
        } else {
            return $this->belongsTo(\VCComponent\Laravel\User\Entities\User::class);
        }
    }

    /**
     * get product raw name.
     * 
     * @return string
     */
    public function getRawName()
    {
        return $this->name;
    }

    /**
     * get product formated name.
     * 
     * @param Collection|array $attributes
     * @return string
     */
    public function getFormatedName($attributes = [])
    {
        $attributes_string = $this->formatHtmlAttributes($attributes);

        return "<h1" . $attributes_string . ">" . $this->name . "</h1>";
    }

    /**
     * get product raw thumbnail.
     * 
     * @return string
     */
    public function getRawThumbnail()
    {
        return $this->thumnbail;
    }

    /**
     * get product formated thumbnail.
     * 
     * @return string 
     */
    public function getFormatedThumbnail($lazyload_src_attribute = "src", $alt = null, $attributes = [])
    {
        $attributes_string = $this->formatHtmlAttributes($attributes);

        if ($alt) {
            return '<img ' . $lazyload_src_attribute . '="' . $this->thumbnail . '"'
                . $attributes_string . ' alt="' . $alt . '"/>';
        } else {
            return '<img ' . $lazyload_src_attribute . '="' . $this->thumbnail . '"'
                . $attributes_string . ' alt="' . $this->title . '"/>';
        }
    }

    /**
     * get product link
     * 
     * @return string 
     */
    public function getRawLink()
    {
        return "/" . $this->type . "/" . $this->slug;
    }

    /**
     * get product formated link
     * 
     * @param string $display_text
     * @param Collection|array $attributes
     * @return string 
     */
    public function getFormatedLink($display_text = null, $attributes = [])
    {
        $attributes_string = $this->formatHtmlAttributes($attributes);

        if ($display_text) {
            return '<a href="' . $this->getRawLink() . '"' . $attributes_string . ' hrefLang="' . app()->getLocale() . '">' . $display_text . '</a>';
        } else {
            return '<a href="' . $this->getRawLink() . '"' . $attributes_string . ' hrefLang="' . app()->getLocale() . '">' . $this->title . '</a>';
        }
    }

    /**
     * get product raw quantity.
     * 
     * @return int 
     */
    public function getRawQuantity()
    {
        return $this->quantity;
    }

    /**
     * get product raw sold quantity.
     * 
     * @return int
     */
    public function getRawSoldQuantity()
    {
        return $this->sold_quantity;
    }

    /**
     * get product raw description.
     * 
     * @return string
     */
    public function getRawDescription()
    {
        return $this->description;
    }

    /**
     * get product raw original price unit with price unit or not.
     * 
     * @param bool $with_unit
     * @return string|int
     */
    public function getRawOriginalPrice($with_unit = false)
    {
        if ($with_unit) {
            return $this->original_price . " " . $this->unit_price;
        } else {
            return $this->original_price;
        }
    }

    /**
     * get product raw price unit with price unit or not.
     * 
     * @param bool $with_unit
     * @return string|int
     */
    public function getRawPrice($with_unit = false)
    {
        if ($with_unit) {
            return $this->price . " " . $this->unit_price;
        } else {
            return $this->price;
        }
    }

    /**
     * get product raw author name.
     * 
     * @param string $display_text
     * @param Collection|array $attributes
     * @return string|null 
     */
    public function getRawAuthorName()
    {
        if ($this->author) {
            return $this->author->first_name . " " . $this->author->last_name;
        } else {
            return null;
        }
    }

    /**
     * get product raw created at
     * 
     * @param string $format
     * @param string $tz
     * @param string $lang
     * @return string 
     */
    public function getRawCreatedAt($format = null, $tz = null, $lang = 'en')
    {
        $created_at = $this->published_date ? $this->published_date : $this->created_at;

        if ($format) {
            $tz = $tz ? $tz : config('app.timezone');
            return Carbon::createFromTimeString($created_at)->setTimezone($tz)->locale($lang)->isoFormat($format);
        } else {
            return $created_at;
        }
    }

    /**
     * format attribute to string to be able to merge with html string
     * 
     * @param Collection|array $attributes
     * @return string
     */
    private function formatHtmlAttributes($attributes)
    {
        if (!$attributes instanceof Collection) $attributes = collect($attributes);

        $attributes_string = '';
        $attributes->each(function ($value, $attribute) use ($attributes_string) {
            $attributes_string .= ' "' . $attribute . '"="' . $value . '"';
        });

        return $attributes_string;
    }
}
