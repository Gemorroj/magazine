<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Product;
use Imagine\Image\ImageInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
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
     */
    public function getProductAction(string $productId): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $product = $manager->find(Product::class, $productId);

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }

    /**
     * @Route("/api/public/photos/{photoId}", methods={"GET"}, defaults={"_format": "image"})
     * @SWG\Response(
     *     response=200,
     *     description="Фото"
     * )
     * @SWG\Parameter(
     *     name="photoId",
     *     in="path",
     *     type="integer",
     *     description="ID фото"
     * )
     */
    public function getPhotoPreviewAction(string $photoId): StreamedResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $photo = $manager->find(Photo::class, $photoId);

        $image = (new \Imagine\Gd\Imagine())
            ->open($this->getParameter('kernel.upload_dir').'/..'.$photo->getPath())
            ->thumbnail(new \Imagine\Image\Box(350, 260), ImageInterface::THUMBNAIL_OUTBOUND)
        ;

        $response = new StreamedResponse();
        $response->setCallback(function () use ($image) {
            $image->show('jpeg');
        });

        $cacheSeconds = 7 * 86400; // 7 дней
        $response->setCache([
            'public' => true,
            'private' => false,
            'immutable' => true,
            'max_age' => $cacheSeconds,
        ]);
        $response->headers->set('X-Accel-Expires', $cacheSeconds);

        return $response;
    }
}
