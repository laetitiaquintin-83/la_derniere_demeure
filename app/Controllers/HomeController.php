<?php

class HomeController
{
    private HomePageModel $model;

    public function __construct(HomePageModel $model)
    {
        $this->model = $model;
    }

    public function index(): array
    {
        $homeData = $this->model->getFeaturedCategories(4);

        return [
            'nombre_articles' => isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0,
            'categories' => $homeData['categories'],
            'category_images' => $homeData['category_images'],
            'titres_poetiques' => $this->model->getPoeticTitles(),
        ];
    }
}