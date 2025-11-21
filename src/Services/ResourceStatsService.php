<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Repository\ResourceRepository;
use MongoDB\BSON\Int64;

class ResourceStatsService
{
    private ResourceRepository $resourceRepository;
    private CategoryRepository $categoryRepositor;
    public function __construct(ResourceRepository $resourceRepository, CategoryRepository $categoryRepository)
    {
        $this->resourceRepository = $resourceRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Retourne le nombre total de ressources disponibles.
     */
    public function getTotalResourcesCount(): Int
    {
        return $this->resourceRepository->count([]);
    }

    /**
     * Retourne le nombre de ressources par catégorie.
     * Le format est un tableau associatif : ['Nom de la catégorie' => Nombre, ...].
     */
    public function getResourcesCountByCategory(): array
    {
        $stats = [];
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            $stats[$category->getName()] = $this->resourceRepository->count(['category' => $category]);
        }

        return $stats;
    }
}

