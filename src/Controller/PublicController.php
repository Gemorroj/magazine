<?php

namespace App\Controller;

use App\Document\Category;
use App\Document\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getCategoriesAction(): JsonResponse
    {
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repository = $manager->getRepository(Category::class);
        $categories = $repository->findAll();

        return $this->json($categories, 200, [], ['groups' => ['categories']]);
    }

    /**
     * @param string $categoryId
     * @return JsonResponse
     */
    public function getCategoryProductsAction(string $categoryId): JsonResponse
    {
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repository = $manager->getRepository(Category::class);
        /** @var Category $category */
        $category = $repository->find($categoryId);
        $products = $category->getProducts();

        return $this->json($products, 200, [], ['groups' => ['products']]);
    }

    /**
     * @param string $productId
     * @return JsonResponse
     */
    public function getProductAction(string $productId): JsonResponse
    {
        $manager = $this->get('doctrine_mongodb')->getManager();
        $repository = $manager->getRepository(Product::class);
        $product = $repository->find($productId);

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }
}
