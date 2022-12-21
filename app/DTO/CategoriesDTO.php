<?php

namespace App\DTO;

use Illuminate\Support\Collection as BaseCollection;

class CategoriesDTO
{
    private BaseCollection $catalogs;
    private BaseCollection $parentCatalogs;
    
    public function __construct(BaseCollection $catalogs, BaseCollection $parentCatalogs)
    {
        $this->catalogs = $catalogs;
        $this->parentCatalogs = $parentCatalogs;
    }
    
    public function getCatalogs(): BaseCollection
    {
        return $this->catalogs;
    }
    
    public function setCatalogs(BaseCollection $catalogs): self
    {
        $this->catalogs = $catalogs;
        
        return $this;
    }
    
    public function getParentCatalogs(): BaseCollection
    {
        return $this->parentCatalogs;
    }
    
    public function setParentCatalogs(BaseCollection $parentCatalogs): self
    {
        $this->parentCatalogs = $parentCatalogs;
        
        return $this;
    }
}
