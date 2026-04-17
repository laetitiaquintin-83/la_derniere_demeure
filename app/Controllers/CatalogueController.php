<?php

class CatalogueController
{
    private CatalogueModel $model;

    public function __construct(CatalogueModel $model)
    {
        $this->model = $model;
    }

    public function index(): array
    {
        $categories = $this->model->getCategories();
        $requestedCategory = $this->model->resolveRequestedCategory($_GET['cat'] ?? null, $categories);
        $categoriesToShow = $requestedCategory ? [$requestedCategory] : $categories;

        return [
            'nombre_articles' => isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0,
            'categories_db' => $categories,
            'categories_to_show' => $categoriesToShow,
            'catalogue_sections' => $this->model->getSections($categoriesToShow),
            'selected_category' => $requestedCategory,
            'titres_poetiques' => $this->model->getPoeticTitles(),
        ];
    }
}