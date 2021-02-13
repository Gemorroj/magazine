<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Product;
use Imagine\Image\ImageInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/api/public/categories", methods={"GET"}, defaults={"_format": "json"})
     * @OA\Response(
     *     response=200,
     *     description="Список категорий",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Category::class, groups={"category"}))
     *     )
     * )
     */
    public function getCategoriesAction(): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Category::class);
        /** @var Category[] $categories */
        $categories = $repository->findAll();

        return $this->json($categories, 200, [], ['groups' => ['category']]);
    }

    /**
     * @Route("/api/public/categories/{categoryId}/products", methods={"GET"}, defaults={"_format": "json"}, requirements={"categoryId": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="Товары в категории",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Product::class, groups={"product"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="categoryId",
     *     in="path",
     *     @OA\Schema(type="integer"),
     *     description="ID категории"
     * )
     */
    public function getCategoryProductsAction(int $categoryId): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var Category|null $category */
        $category = $manager->find(Category::class, $categoryId);
        if (!$category) {
            throw $this->createNotFoundException();
        }
        $products = $category->getProducts();

        return $this->json($products, 200, [], ['groups' => ['product']]);
    }

    /**
     * @Route("/api/public/products/{productId}", methods={"GET"}, defaults={"_format": "json"}, requirements={"productId": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="Товар",
     *     @OA\JsonContent(ref=@Model(type=Product::class, groups={"product"}))
     * )
     * @OA\Parameter(
     *     name="productId",
     *     in="path",
     *     @OA\Schema(type="integer"),
     *     description="ID товара"
     * )
     */
    public function getProductAction(int $productId): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var Product|null $product */
        $product = $manager->find(Product::class, $productId);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }

    /**
     * @Route("/api/public/photos/{photoId}", methods={"GET"}, defaults={"_format": "image"}, requirements={"photoId": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="Фото"
     * )
     * @OA\Parameter(
     *     name="photoId",
     *     in="path",
     *     @OA\Schema(type="integer"),
     *     description="ID фото"
     * )
     */
    public function getPhotoPreviewAction(int $photoId): StreamedResponse
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var Photo|null $photo */
        $photo = $manager->find(Photo::class, $photoId);
        if (!$photo) {
            throw $this->createNotFoundException();
        }

        $image = (new \Imagine\Gd\Imagine())
            ->open($this->getParameter('kernel.upload_dir').'/..'.$photo->getPath())
            ->thumbnail(new \Imagine\Image\Box(350, 260), ImageInterface::THUMBNAIL_OUTBOUND)
        ;

        $response = new StreamedResponse();
        $response->setCallback(static function () use ($image): void {
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
