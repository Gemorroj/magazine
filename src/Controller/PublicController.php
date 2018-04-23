<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use VK\Client\Enums\VKLanguage;
use VK\Client\VKApiClient;

class PublicController extends Controller
{
    /**
     * @Route("/api/public/categories", methods={"GET"}, defaults={"_format": "json"})
     * @SWG\Response(
     *     response=200,
     *     description="Список категорий",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class, groups={"category"}))
     *     )
     * )
     * @return JsonResponse
     */
    public function getCategoriesAction(): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Category::class);
        $categories = $repository->findAll();

        return $this->json($categories, 200, [], ['groups' => ['category']]);
    }

    /**
     * @Route("/api/public/categories/{categoryId}/products", methods={"GET"}, defaults={"_format": "json"})
     * @SWG\Response(
     *     response=200,
     *     description="Товары в категории",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class, groups={"product"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="categoryId",
     *     in="path",
     *     type="integer",
     *     description="ID категории"
     * )
     * @param string $categoryId
     * @return JsonResponse
     */
    public function getCategoryProductsAction(string $categoryId): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Category::class);
        /** @var Category $category */
        $category = $repository->find($categoryId);
        $products = $category->getProducts();

        return $this->json($products, 200, [], ['groups' => ['product']]);
    }

    /**
     * @Route("/api/public/products/{productId}", methods={"GET"}, defaults={"_format": "json"})
     * @SWG\Response(
     *     response=200,
     *     description="Товар",
     *     @Model(type=Product::class, groups={"product"})
     * )
     * @SWG\Parameter(
     *     name="productId",
     *     in="path",
     *     type="integer",
     *     description="ID товара"
     * )
     * @param string $productId
     * @return JsonResponse
     */
    public function getProductAction(string $productId): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Product::class);
        $product = $repository->find($productId);

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }


    /**
     * @todo
     * @Route("/api/public/market", methods={"GET"}, defaults={"_format": "json"})
     * @SWG\Response(
     *     response=200,
     *     description="Вк"
     * )
     * @return JsonResponse
     */
    public function listMarket(): JsonResponse
    {
        $vk = new VKApiClient('5.74', VKLanguage::RUSSIAN);
        $response = $vk->market()->get($this->getParameter('vk_token'), [
            'owner_id' => -165538977,
            'extended' => true,
        ]);

        return $this->json($response);
    }
}
