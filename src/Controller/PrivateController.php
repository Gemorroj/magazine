<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PrivateController extends AbstractController
{
    /**
     * @Route("/api/private/login", methods={"POST"}, defaults={"_format": "json"})
     * @OA\Response(
     *     @OA\Header(header="Authorization", description="Bearer токен", @OA\Schema(type="string")),
     *     response=200,
     *     description="OK"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Ошибка валидации"
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             required={"login", "password"},
     *             @OA\Property(
     *                 property="login",
     *                 type="string",
     *                 description="Логин"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="Пароль"
     *             )
     *         )
     *     )
     * )
     */
    public function loginAction(Request $request): JsonResponse
    {
        $login = $request->get('login');
        $password = $request->get('password');

        if ($login === $this->getParameter('login') && $password === $this->getParameter('password')) {
            return $this->json([
                'status' => 'success',
            ], 200, ['Authorization' => 'Bearer '.\hash_hmac('sha256', $login.':'.$password, $this->getParameter('kernel.secret'))]);
        }

        throw new UnauthorizedHttpException('Bearer');
    }

    private function checkAuth(Request $request): void
    {
        $expectedHash = \hash_hmac('sha256', $this->getParameter('login').':'.$this->getParameter('password'), $this->getParameter('kernel.secret'));
        \preg_match('/Bearer\s+(?P<token>\S+)/', $request->headers->get('Authorization'), $matches);

        if (!$matches || $matches['token'] !== $expectedHash) {
            throw new HttpException(403, 'Ны не авторизованы');
        }
    }

    /**
     * @Route("/api/private/categories", methods={"POST"}, defaults={"_format": "json"})
     * @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             required={"categoryName"},
     *             @OA\Property(
     *                 property="categoryName",
     *                 type="string",
     *                 description="Имя категории"
     *             )
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="category")
     */
    public function addCategoryAction(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $category = new Category();
        $category->setName($request->request->get('categoryName'));

        $errors = $validator->validate($category);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($category);
        $manager->flush();

        return $this->json($category, 201, [], ['groups' => ['category']]);
    }

    /**
     * @Route("/api/private/categories/{id}", methods={"PUT"}, defaults={"_format": "json"}, requirements={"id": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref=@Model(type=Category::class, groups={"category"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             required={"categoryId", "categoryName"},
     *             @OA\Property(
     *                 property="categoryId",
     *                 type="integer",
     *                 description="ID категории"
     *             ),
     *             @OA\Property(
     *                 property="categoryName",
     *                 type="string",
     *                 description="Имя категории"
     *             )
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="category")
     */
    public function updateCategoryAction(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $manager = $this->getDoctrine()->getManager();

        /** @var Category|null $category */
        $category = $manager->find(Category::class, $id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        $category->setDateUpdate(new \DateTime());
        $category->setName($request->request->get('categoryName'));

        $errors = $validator->validate($category);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $manager->persist($category);
        $manager->flush();

        return $this->json($category, 200, [], ['groups' => ['category']]);
    }

    /**
     * @Route("/api/private/products/{id}", methods={"DELETE"}, defaults={"_format": "json"}, requirements={"id": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="OK"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="product")
     */
    public function deleteProductAction(int $id, Request $request): JsonResponse
    {
        $this->checkAuth($request);

        $manager = $this->getDoctrine()->getManager();

        /** @var Product|null $product */
        $product = $manager->find(Product::class, $id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        // вручную очищаем сущность, т.к. sqlite в doctrine не поддерживает foreign keys
        // @see https://github.com/doctrine/dbal/issues/2833
        $product->getPhotos()->clear();
        $manager->remove($product);
        $manager->flush();

        return $this->json(null);
    }

    /**
     * @Route("/api/private/categories/{id}", methods={"DELETE"}, defaults={"_format": "json"}, requirements={"id": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="OK"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="category")
     */
    public function deleteCategoryAction(int $id, Request $request): JsonResponse
    {
        $this->checkAuth($request);

        $manager = $this->getDoctrine()->getManager();

        /** @var Category|null $category */
        $category = $manager->find(Category::class, $id);
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

        $manager->remove($category);
        $manager->flush();

        return $this->json(null);
    }

    /**
     * @Route("/api/private/photo", methods={"POST"}, defaults={"_format": "json"})
     * @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="name", type="string", description="Имя загруженного файла"),
     *         @OA\Property(property="path", type="string", description="Публичный путь к загруженному файлу"),
     *     )
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             required={"file"},
     *             @OA\Property(
     *                 property="file",
     *                 type="string",
     *                 format="binary",
     *                 description="Фотография"
     *             )
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="photo")
     */
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

    /**
     * @Route("/api/private/products/{id}", methods={"PUT"}, defaults={"_format": "json"}, requirements={"id": "\d+"})
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref=@Model(type=Product::class, groups={"product"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             required={"name", "description", "price", "photos"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Имя товара"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Описание товара"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 description="Цена"
     *             ),
     *             @OA\Property(
     *                 property="size",
     *                 type="string",
     *                 description="Размер"
     *             ),
     *             @OA\Property(
     *                 property="composition",
     *                 type="string",
     *                 description="Состав"
     *             ),
     *             @OA\Property(
     *                 property="manufacturer",
     *                 type="string",
     *                 description="Производитель"
     *             ),
     *             @OA\Property(
     *                 property="photos",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 description="Фотографии"
     *             )
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="product")
     */
    public function updateProductAction(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $manager = $this->getDoctrine()->getManager();

        /** @var Product|null $product */
        $product = $manager->find(Product::class, $id);
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

        /** @var PersistentCollection $photos */
        $photos = $product->getPhotos();

        // оставляем только те фотографии, что пришли из формы
        foreach ($photos as $p) {
            if (!\in_array($p->getPath(), $request->request->get('photos', []), true)) {
                $photos->removeElement($p);
            }
        }

        foreach ($request->request->get('photos', []) as $photoPath) {
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

        $manager->persist($product);
        $manager->flush();

        $sortedPhotos = $product->getPhotos()->toArray();
        \sort($sortedPhotos);
        $product->setPhotos(new ArrayCollection($sortedPhotos)); // сбрасываем сортировку

        return $this->json($product, 200, [], ['groups' => ['product']]);
    }

    /**
     * @Route("/api/private/products", methods={"POST"}, defaults={"_format": "json"})
     * @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(ref=@Model(type=Product::class, groups={"product"}))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             required={"categoryId", "name", "description", "price", "photos"},
     *             @OA\Property(
     *                 property="categoryId",
     *                 type="integer",
     *                 description="ID категории"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Имя товара"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Описание товара"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 description="Цена"
     *             ),
     *             @OA\Property(
     *                 property="size",
     *                 type="string",
     *                 description="Размер"
     *             ),
     *             @OA\Property(
     *                 property="composition",
     *                 type="string",
     *                 description="Состав"
     *             ),
     *             @OA\Property(
     *                 property="manufacturer",
     *                 type="string",
     *                 description="Производитель"
     *             ),
     *             @OA\Property(
     *                 property="photos",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 description="Фотографии"
     *             )
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="product")
     */
    public function addProductAction(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }

        $manager = $this->getDoctrine()->getManager();
        $category = $manager->find(Category::class, $categoryId);

        $product = new Product();
        $product->setCategory($category);
        $product->setDateCreate(new \DateTime());
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setComposition($request->request->get('composition'));
        $product->setManufacturer($request->request->get('manufacturer'));
        $product->setPrice($request->request->get('price'));
        $product->setSize($request->request->get('size'));

        foreach ($request->request->get('photos', []) as $photoPath) {
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

        $manager->persist($product);
        $manager->flush();

        return $this->json($product, 201, [], ['groups' => ['product']]);
    }
}
