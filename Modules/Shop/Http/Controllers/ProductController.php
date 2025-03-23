<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Shop\Repositories\Front\Interfaces\ProductRepositoryInterface;
use Modules\Shop\Repositories\Front\Interfaces\CategoryRepositoryInterface;
use Modules\Shop\Repositories\Front\Interfaces\TagRepositoryInterface;

class ProductController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    protected $tagRepository;
    protected $defaultPriceRange;
    protected $sortingQuery;
    protected $perPage;

    public function __construct(ProductRepositoryInterface $productRepository, CategoryRepositoryInterface $categoryRepository, TagRepositoryInterface $tagRepository)
    {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->defaultPriceRange = [
            'min' => 10000,
            'max' => 75000,
        ];
        $this->perPage = 10;

        $this->data['categories'] = $this->categoryRepository->findAll();
        $this->data['filter']['price'] = $this->defaultPriceRange;

        $this->sortingQuery = null;
        $this->data['sortingQuery'] = $this->sortingQuery;
        $this->data['sortingOptions'] = [
            '' => '-- Sort Products --',
            url()->current() . '?sort=price&order=asc' => 'Price: Low to High',
            url()->current() . '?sort=price&order=desc' => 'Price: High to Low',
            url()->current() . '?sort=publish_date&order=desc' => 'Newest Item',        
        ];
    }

    public function index(Request $request)
    {
        $priceFilter = $this->getPriceRangeFilter($request);

        $options = [
            'per_page' => $this->perPage,
            'filter' => [
                'price' => $priceFilter,
            ],
        ];

        if ($request->get('price')) {
            $this->data['filter']['price'] = $priceFilter;
        }

        if ($request->get('sort')) {
            $sorting = $this->sortingRequest($request);
            $options['sorting'] = $sorting;

            $this->sortingQuery = http_build_query($sorting);
            $this->data['sortingQuery'] = '?' . $this->sortingQuery;
        }

        $this->data['products'] = $this->productRepository->findAll($options);

        return $this->loadTheme('products.index', $this->data);
    }

    public function category($categorySlug)
    {
        $category = $this->categoryRepository->findBySlug($categorySlug);

        $options = [
            'per_page' => $this->perPage,
            'filter' => [
                'category' => $categorySlug,
            ],
        ];

        $this->data['products'] = $this->productRepository->findAll($options);
        $this->data['category'] = $category;

        return $this->loadTheme('products.category', $this->data);
    }

    public function tag($tagSlug)
    {
        $tag = $this->tagRepository->findBySlug($tagSlug);

        $options = [
            'per_page' => $this->perPage,
            'filter' => [
                'tag' => $tagSlug,
            ],
        ];

        $this->data['products'] = $this->productRepository->findAll($options);
        $this->data['tag'] = $tag;

        return $this->loadTheme('products.tag', $this->data);
    }

    public function getPriceRangeFilter(Request $request)
    {
        if (!$request->get('price')) {
            return $this->defaultPriceRange;
        }

        $prices = explode('-', $request->get('price'));
        if (count($prices) < 2) {
            return $this->defaultPriceRange;
        }

        return [
            'min' => (int) $prices[0],
            'max' => (int) $prices[1],
        ];
    }

    public function sortingRequest(Request $request)
    {
        $sort = [];
        $allowedSortFields = ['price', 'publish_date'];

        if (in_array($request->get('sort'), $allowedSortFields)) {
            $sort['sort'] = $request->get('sort');
            $sort['order'] = $request->get('order') ?? 'desc';
        }

        return $sort;
    }
}