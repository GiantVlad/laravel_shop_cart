<?php

namespace App\Http\Controllers;

use App\DTO\CategoriesDTO;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\PropertiesResource;
use App\Product;
use App\Property;
use App\Repositories\PropertyRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\View\View as ViewInstance;
use \Illuminate\Contracts\View\Factory as ViewFactoryContract;
use App\Catalog;

class FilterController extends Controller
{
    public function __construct(
        private readonly PropertyRepository $propertyRepository
    ) {}
    
    public function getFilterProperties(): ResourceCollection
    {
        return new PropertiesResource($this->propertyRepository->getFilteredProducts());
    }
}
