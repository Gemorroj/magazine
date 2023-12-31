<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\PhotoRepository;
use App\Repository\ProductRepository;
use Imagine\Image\ManipulatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/public')]
final class PublicController extends AbstractController
{
    #[Route(path: '/categories', defaults: ['_format' => 'json'], methods: ['GET'], stateless: true)]
    #[OA\Response(
        response: 200,
        description: 'Список категорий',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class, groups: ['category']), nullable: false),
            nullable: false,
        )
    )]
    #[OA\Tag(name: 'category')]
    public function getCategoriesAction(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        return $this->json($categories, 200, [], ['groups' => ['category']]);
    }

    #[Route(path: '/categories/{categoryId}/products', requirements: ['categoryId' => '\d+'], defaults: ['_format' => 'json'], methods: ['GET'], stateless: true)]
    #[OA\Response(
        response: 200,
        description: 'Товары в категории',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product']), nullable: false),
            nullable: false,
        )
    )]
    #[OA\Tag(name: 'product')]
    #[OA\Parameter(name: 'categoryId', description: 'ID категории', in: 'path', required: true, schema: new OA\Schema(type: 'integer', nullable: false))]
    public function getCategoryProductsAction(int $categoryId, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find($categoryId);
        if (!$category) {
            throw $this->createNotFoundException();
        }
        $products = $category->getProducts();

        return $this->json($products, 200, [], ['groups' => ['product']]);
    }

    #[Route(path: '/products/{productId}', requirements: ['productId' => '\d+'], defaults: ['_format' => 'json'], methods: ['GET'], stateless: true)]
    #[OA\Response(
        response: 200,
        description: 'Товар',
        content: new OA\JsonContent(
            ref: new Model(type: Product::class, groups: ['product']),
            nullable: false,
        ),
    )]
    #[OA\Tag(name: 'product')]
    #[OA\Parameter(name: 'productId', description: 'ID товара', in: 'path', required: true, schema: new OA\Schema(type: 'integer', nullable: false))]
    public function getProductAction(int $productId, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($productId);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }

    #[Route(path: '/photos/{photoId}', requirements: ['photoId' => '\d+'], defaults: ['_format' => 'image'], methods: ['GET'], stateless: true)]
    #[OA\Response(
        response: 200,
        description: 'Фото',
        content: new OA\MediaType(
            mediaType: 'image/jpeg',
            schema: new OA\Schema(type: 'string', format: 'binary')
        ),
    )]
    #[OA\Tag(name: 'photo')]
    #[OA\Parameter(name: 'photoId', description: 'ID фото', in: 'path', required: true, schema: new OA\Schema(type: 'integer', nullable: false))]
    public function getPhotoPreviewAction(int $photoId, PhotoRepository $photoRepository): StreamedResponse
    {
        $photo = $photoRepository->find($photoId);
        if (!$photo) {
            throw $this->createNotFoundException();
        }

        $image = (new \Imagine\Gd\Imagine())
            ->open($this->getParameter('kernel.upload_dir').'/..'.$photo->getPath())
            ->thumbnail(new \Imagine\Image\Box(350, 260), ManipulatorInterface::THUMBNAIL_OUTBOUND)
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
