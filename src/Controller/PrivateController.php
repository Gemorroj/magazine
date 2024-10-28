<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/api/private')]
final class PrivateController extends AbstractController
{
    #[Route(path: '/login', defaults: ['_format' => 'json'], methods: ['POST'])]
    #[OA\Response(
        response: 204,
        description: 'Success',
        headers: [
            new OA\Header(header: 'Authorization', description: 'Bearer токен', schema: new OA\Schema(type: 'string', nullable: false)),
        ]
    )]
    #[OA\Response(
        response: 401,
        description: 'Error',
    )]
    #[OA\RequestBody(
        required: true,
        content: [new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['login', 'password'],
                properties: [
                    new OA\Property(property: 'login', type: 'string', nullable: false),
                    new OA\Property(property: 'password', type: 'string', format: 'password', nullable: false),
                ]
            )
        )],
    )]
    public function loginAction(Request $request): Response
    {
        $login = $request->get('login');
        $password = $request->get('password');

        if ($login === $this->getParameter('login') && $password === $this->getParameter('password')) {
            return new Response(null, 204, ['Authorization' => 'Bearer '.\hash_hmac('sha256', $login.':'.$password, $this->getParameter('kernel.secret'))]);
        }

        throw new UnauthorizedHttpException('Bearer');
    }

    private function checkAuth(Request $request): void
    {
        $expectedHash = \hash_hmac('sha256', $this->getParameter('login').':'.$this->getParameter('password'), $this->getParameter('kernel.secret'));
        \preg_match('/Bearer\s+(?P<token>\S+)/', $request->headers->get('Authorization'), $matches);

        if (!$matches || $matches['token'] !== $expectedHash) {
            throw new HttpException(403, 'Вы не авторизованы');
        }
    }

    #[Route(path: '/categories', defaults: ['_format' => 'json'], methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 201,
        description: 'Success',
        content: new OA\JsonContent(
            ref: new Model(type: Category::class, groups: ['category']),
            nullable: false,
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\RequestBody(
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['categoryName'],
                properties: [
                    new OA\Property(
                        property: 'categoryName',
                        description: 'Имя категории',
                        type: 'string',
                        nullable: false
                    ),
                ]
            )
        )],
    )]
    #[OA\Tag(name: 'category')]
    public function addCategoryAction(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->checkAuth($request);

        $category = new Category();
        $category->setName($request->request->get('categoryName'));

        $errors = $validator->validate($category);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json($category, 201, [], ['groups' => ['category']]);
    }

    #[Route(path: '/categories/{id}', requirements: ['id' => '\d+'], defaults: ['_format' => 'json'], methods: ['PUT'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            ref: new Model(type: Category::class, groups: ['category']),
            nullable: false,
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\RequestBody(
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['categoryId', 'categoryName'],
                properties: [
                    new OA\Property(
                        property: 'categoryId',
                        description: 'ID категории',
                        type: 'integer',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'categoryName',
                        description: 'Имя категории',
                        type: 'string',
                        nullable: false
                    ),
                ]
            )
        )],
    )]
    #[OA\Tag(name: 'category')]
    public function updateCategoryAction(int $id, Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->checkAuth($request);

        /** @var Category|null $category */
        $category = $entityManager->find(Category::class, $id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        $category->setDateUpdate(new \DateTime());
        $category->setName($request->request->get('categoryName'));

        $errors = $validator->validate($category);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json($category, 200, [], ['groups' => ['category']]);
    }

    #[Route(path: '/products/{id}', requirements: ['id' => '\d+'], defaults: ['_format' => 'json'], methods: ['DELETE'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 204,
        description: 'Success',
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\Tag(name: 'product')]
    public function deleteProductAction(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->checkAuth($request);

        /** @var Product|null $product */
        $product = $entityManager->find(Product::class, $id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        // вручную очищаем сущность, т.к. sqlite в doctrine не поддерживает foreign keys
        // @see https://github.com/doctrine/dbal/issues/2833
        $product->getPhotos()->clear();
        $entityManager->remove($product);
        $entityManager->flush();

        return new Response(null, 204);
    }

    #[Route(path: '/categories/{id}', requirements: ['id' => '\d+'], defaults: ['_format' => 'json'], methods: ['DELETE'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 204,
        description: 'Success',
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\Tag(name: 'category')]
    public function deleteCategoryAction(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->checkAuth($request);

        /** @var Category|null $category */
        $category = $entityManager->find(Category::class, $id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        // вручную очищаем сущность, т.к. sqlite в doctrine не поддерживает foreign keys
        // @see https://github.com/doctrine/dbal/issues/2833
        /** @var Product $product */
        foreach ($category->getProducts() as $product) {
            $product->getPhotos()->clear();
        }
        $category->getProducts()->clear();

        $entityManager->remove($category);
        $entityManager->flush();

        return new Response(null, 204);
    }

    #[Route(path: '/photo', defaults: ['_format' => 'json'], methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 201,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'name',
                    description: 'Имя загруженного файла',
                    type: 'string',
                    nullable: false,
                ),
                new OA\Property(
                    property: 'path',
                    description: 'Публичный путь к загруженному файлу',
                    type: 'string',
                    nullable: false
                ),
            ],
            type: 'object',
            nullable: false,
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\RequestBody(
        required: true,
        content: [new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['file'],
                properties: [
                    new OA\Property(
                        property: 'file',
                        description: 'Фотография',
                        type: 'string',
                        format: 'binary',
                        nullable: false,
                    ),
                ]
            )
        )],
    )]
    #[OA\Tag(name: 'photo')]
    public function addPhotoAction(Request $request, Filesystem $filesystem): JsonResponse
    {
        $this->checkAuth($request);

        if (!$request->files->has('file')) {
            throw new \InvalidArgumentException('Не передана фотография');
        }

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile->isValid()) {
            throw new UploadException($uploadedFile->getErrorMessage());
        }
        if ('image' !== \strstr($uploadedFile->getMimeType(), '/', true)) {
            throw new \InvalidArgumentException('Некорректный mime тип. Поддерживаются только картинки (image/*)');
        }

        $dirName = \date('Y-m-d');
        $filesystem->mkdir($this->getParameter('kernel.upload_dir').'/'.$dirName);

        $fileName = \str_replace('.', '', \uniqid('', true));
        if ($extension = $uploadedFile->guessExtension()) {
            $fileName = \sprintf('%s.%s', $fileName, $extension);
        }

        $file = $uploadedFile->move($this->getParameter('kernel.upload_dir').'/'.$dirName, $fileName);

        return $this->json([
            'name' => $file->getFilename(),
            'path' => '/upload/'.$dirName.'/'.$file->getFilename(),
        ], 201);
    }

    #[Route(path: '/products/{id}', requirements: ['id' => '\d+'], defaults: ['_format' => 'json'], methods: ['PUT'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            ref: new Model(type: Product::class, groups: ['product']),
            nullable: false,
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\RequestBody(
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['name', 'description', 'price', 'photos'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'number',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'size',
                        type: 'string',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'composition',
                        type: 'string',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'manufacturer',
                        type: 'string',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'photos',
                        type: 'array',
                        items: new OA\Items(type: 'string', nullable: false),
                        nullable: false
                    ),
                ]
            )
        )],
    )]
    #[OA\Tag(name: 'product')]
    public function updateProductAction(int $id, Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->checkAuth($request);

        /** @var Product|null $product */
        $product = $entityManager->find(Product::class, $id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $product->setDateUpdate(new \DateTime());
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setComposition($request->request->get('composition'));
        $product->setManufacturer($request->request->get('manufacturer'));
        $product->setPrice($request->request->get('price'));
        $product->setSize($request->request->get('size'));

        $photos = $product->getPhotos();

        $requestPhotos = $request->request->all('photos');

        // оставляем только те фотографии, что пришли из формы
        foreach ($photos as $p) {
            if (!\in_array($p->getPath(), $requestPhotos, true)) {
                $photos->removeElement($p);
            }
        }

        foreach ($requestPhotos as $photoPath) {
            if (!$photos->exists(static function ($key, Photo $photo) use ($photoPath): bool {
                return $photo->getPath() === $photoPath;
            })) {
                // добавляем новые, если их раньше не было
                $photos->add(
                    (new Photo())
                        ->setDateCreate(new \DateTime())
                        ->setProduct($product)
                        ->setPath($photoPath)
                );
            }
        }

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $entityManager->persist($product);
        $entityManager->flush();

        $sortedPhotos = $product->getPhotos()->toArray();
        \sort($sortedPhotos);
        $product->setPhotos(new ArrayCollection($sortedPhotos)); // сбрасываем сортировку

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }

    #[Route(path: '/products', defaults: ['_format' => 'json'], methods: ['POST'])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 201,
        description: 'Success',
        content: new OA\JsonContent(
            ref: new Model(type: Product::class, groups: ['product']),
            nullable: false,
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
    )]
    #[OA\RequestBody(
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['categoryId', 'name', 'description', 'price', 'photos'],
                properties: [
                    new OA\Property(
                        property: 'categoryId',
                        type: 'integer',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'number',
                        nullable: false
                    ),
                    new OA\Property(
                        property: 'size',
                        type: 'string',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'composition',
                        type: 'string',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'manufacturer',
                        type: 'string',
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'photos',
                        type: 'array',
                        items: new OA\Items(type: 'string', nullable: false),
                        nullable: false
                    ),
                ]
            )
        )],
    )]
    #[OA\Tag(name: 'product')]
    public function addProductAction(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->checkAuth($request);

        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }

        $category = $entityManager->find(Category::class, $categoryId);
        if (!$category) {
            throw new \InvalidArgumentException('Указанная категория не найдена');
        }

        $product = new Product();
        $product->setCategory($category);
        $product->setDateCreate(new \DateTime());
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setComposition($request->request->get('composition'));
        $product->setManufacturer($request->request->get('manufacturer'));
        $product->setPrice($request->request->get('price'));
        $product->setSize($request->request->get('size'));

        $requestPhotos = $request->request->all('photos');
        foreach ($requestPhotos as $photoPath) {
            $product->getPhotos()->add(
                (new Photo())
                    ->setDateCreate(new \DateTime())
                    ->setProduct($product)
                    ->setPath($photoPath)
            );
        }

        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product, 201, [], ['groups' => ['product']]);
    }
}
