<?php namespace Feegleweb\OctoshopLite\Components;

use Cms\Classes\ComponentBase;
use Feegleweb\OctoshopLite\Models\Category as ShopCategory;
use Feegleweb\OctoshopLite\Models\Product as ShopProduct;

class Product extends ComponentBase
{
    private $slug;
    private $basket;
    private $mainImageSize;
    private $subImageSize;
    private $product;
    private $category;

    public function componentDetails()
    {
        return [
            'name'        => 'Shop Product',
            'description' => 'Display a single product',
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title' => 'Slug',
                'default' => '{{ :slug }}',
                'type' => 'string',
            ],
            'basket' => [
                'title' => 'Basket container element',
                'description' => 'Basket container element to update when adding products to cart',
            ],
            'mainImageSize' => [
                'title' => 'Main Image Size',
            ],
            'subImageSize' => [
                'title' => 'Thumbnail Size',
            ],
            'category' => [
                'title' => 'Category',
            ]
        ];
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->product = $this->page['product'] = $this->loadProduct();
    }

    public function prepareVars()
    {
        $this->slug = $this->page['slug'] = $this->property('slug');
        $this->basket = $this->page['basket'] = $this->property('basket');
        $this->mainImageSize = $this->page['mainImageSize'] = $this->property('mainImageSize');
        $this->subImageSize = $this->page['subImageSize'] = $this->property('subImageSize');
        $this->category = $this->property('category');
    }

    public function loadProduct()
    {
        $product = ShopProduct::whereSlug($this->slug)->with(['images' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        }])->first();
        
        if (empty($this->category)) {
            return $product;
        }
        
        // check category
        foreach ($product->categories as $category) {
            if (strcasecmp($category->slug, $this->category) == 0) {
                return $product;
            }
        }
        return null;
    }
}
