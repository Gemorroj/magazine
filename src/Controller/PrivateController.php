<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

class PrivateController extends Controller
{
    /**
     * @Route("/api/private/login", methods={"POST"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     @SWG\Header(header="Authorization", description="Bearer токен", type="string"),
     *     response=200,
     *     description="OK"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="login",
     *     in="formData",
     *     type="string",
     *     description="Логин",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="Пароль",
     *     required=true
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $login = $request->get('login');
        $password = $request->get('password');

        if ($login === $this->getParameter('login') && $password === $this->getParameter('password')) {
            return $this->json([
                'status' => 'success',
            ], 200, ['Authorization' => 'Bearer ' . \hash_hmac('sha256', $login.':'.$password, $this->getParameter('kernel.secret'))]);
        }

        return $this->json([
            'status' => 'error',
        ], 401);
    }


    /**
     * @param Request $request
     */
    private function checkAuth(Request $request): void
    {
        $expectedHash = \hash_hmac('sha256', $this->getParameter('login').':'.$this->getParameter('password'), $this->getParameter('kernel.secret'));
        \preg_match('/Bearer\s+(?P<token>\S+)/', $request->headers->get('Authorization'), $matches);

        if (!$matches || $matches['token'] !== $expectedHash) {
            throw new HttpException(403, 'Ны не авторизованы');
        }
    }


    /**
     * @Route("/api/private/categories/add", methods={"POST"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @Model(type=Category::class, groups={"category"}))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="categoryName",
     *     in="formData",
     *     type="string",
     *     description="Имя категории",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="category")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function addCategoryAction(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $category = new Category();
        $category->setName($request->request->get('categoryName'));

        // валидация
        $errors = $validator->validate($category);
        if ($errors->count() > 0) {
            return $this->json([
                'status' => 'error',
                'message' => (string)$errors,
            ], 400);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($category);
        $manager->flush();

        return $this->json($category, 200, [], ['groups' => ['category']]);
    }


    /**
     * @Route("/api/private/categories/update", methods={"POST", "PUT"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="OK",
     *     @Model(type=Category::class, groups={"category"}))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="categoryId",
     *     in="formData",
     *     type="integer",
     *     description="ID категории",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="categoryName",
     *     in="formData",
     *     type="string",
     *     description="Имя категории",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="category")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function updateCategoryAction(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }

        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Category::class);

        /** @var Category $category */
        $category = $repository->find($categoryId);
        if (null === $category) {
            $this->createNotFoundException('Категория не найдена');
        }

        $category->setDateUpdate(new \DateTime());
        $category->setName($request->request->get('categoryName'));

        // валидация
        $errors = $validator->validate($category);
        if ($errors->count() > 0) {
            return $this->json([
                'status' => 'error',
                'message' => (string)$errors,
            ], 400);
        }

        $manager->persist($category);
        $manager->flush();

        return $this->json($category, 201, [], ['groups' => ['category']]);
    }


    /**
     * @Route("/api/private/products/delete", methods={"POST", "DELETE"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="OK"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="productId",
     *     in="formData",
     *     type="integer",
     *     description="ID товара",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="product")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteProductAction(Request $request): JsonResponse
    {
        $this->checkAuth($request);

        $productId = $request->request->get('productId');
        if (null === $productId || '' === $productId) {
            throw new \InvalidArgumentException('Не указан идентификатор товара');
        }

        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Product::class);

        /** @var Product $product */
        $product = $repository->find($productId);
        if (null === $product) {
            $this->createNotFoundException('Товар не найден');
        }

        // вручную очищаем сущность, т.к. sqlite в doctrine не поддерживает foreign keys
        // @see https://github.com/doctrine/dbal/issues/2833
        $product->getPhotos()->clear();
        $manager->remove($product);
        $manager->flush();

        return $this->json(null);
    }

    /**
     * @Route("/api/private/categories/delete", methods={"POST", "DELETE"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="OK"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="categoryId",
     *     in="formData",
     *     type="integer",
     *     description="ID категории",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="category")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCategoryAction(Request $request): JsonResponse
    {
        $this->checkAuth($request);

        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }

        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Category::class);

        /** @var Category $category */
        $category = $repository->find($categoryId);
        if (null === $category) {
            $this->createNotFoundException('Категория не найдена');
        }

        // вручную очищаем сущность, т.к. sqlite в doctrine не поддерживает foreign keys
        // @see https://github.com/doctrine/dbal/issues/2833
        $category->getProducts()->map(function (Product $product) {
            $product->getPhotos()->clear();
        })->clear();

        $manager->remove($category);
        $manager->flush();

        return $this->json(null);
    }


    /**
     * @Route("/api/private/photo/add", methods={"POST"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="name", type="string", description="Имя загруженного файла"),
     *         @SWG\Property(property="path", type="string", description="Публичный путь к загруженному файлу"),
     *     )
     * )
     * @SWG\Parameter(
     *     name="file",
     *     in="formData",
     *     type="file",
     *     description="Фотография",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="photo")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addPhotoAction(Request $request): JsonResponse
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
        if (\strstr($uploadedFile->getMimeType(), '/', true) !== 'image') {
            throw new \InvalidArgumentException('Некорректный mime тип. Поддерживаются только картинки (image/*)');
        }

        $dirName = \date('Y-m-d');
        $this->get('filesystem')->mkdir($this->getParameter('kernel.upload_dir') . '/' . $dirName);

        $fileName = \str_replace('.', '', \uniqid('', true));
        if ($extension = $uploadedFile->guessExtension()) {
            $fileName = \sprintf('%s.%s', $fileName, $extension);
        }

        $file = $uploadedFile->move($this->getParameter('kernel.upload_dir') . '/' . $dirName, $fileName);

        return $this->json([
            'name' => $file->getFilename(),
            'path' => '/upload/' . $dirName . '/' . $file->getFilename(),
        ]);
    }


    /**
     * @Route("/api/private/products/update", methods={"POST", "PUT"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="OK",
     *     @Model(type=Product::class, groups={"product"}))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     type="integer",
     *     description="ID товара",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     type="string",
     *     description="Имя товара",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     type="string",
     *     description="Описание товара",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="price",
     *     in="formData",
     *     type="number",
     *     description="Цена",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="formData",
     *     type="string",
     *     description="Размер",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="composition",
     *     in="formData",
     *     type="string",
     *     description="Состав",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="manufacturer",
     *     in="formData",
     *     type="string",
     *     description="Производитель",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="photos",
     *     in="formData",
     *     type="array",
     *     @SWG\Items(type="string"),
     *     description="Фотографии",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="product")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function updateProductAction(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->checkAuth($request);

        $productId = $request->request->get('id');
        if (null === $productId || '' === $productId) {
            throw new \InvalidArgumentException('Не указан идентификатор товара');
        }

        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository(Product::class);

        /** @var Product $product */
        $product = $repository->find($productId);
        if (null === $product) {
            $this->createNotFoundException('Товар не найден');
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
            if (!$photos->exists(function ($key, Photo $photo) use ($photoPath) {
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

        // валидация
        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return $this->json([
                'status' => 'error',
                'message' => (string)$errors,
            ], 400);
        }

        $manager->persist($product);
        $manager->flush();

        $sortedPhotos = $product->getPhotos()->toArray();
        \sort($sortedPhotos);
        $product->setPhotos(new ArrayCollection($sortedPhotos)); // сбрасываем сортировку

        return $this->json($product, 201, [], ['groups' => ['product']]);
    }


    /**
     * @Route("/api/private/products/add", methods={"POST"}, defaults={"_format": "json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="OK",
     *     @Model(type=Product::class, groups={"product"}))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Ошибка валидации"
     * )
     * @SWG\Parameter(
     *     name="categoryId",
     *     in="formData",
     *     type="integer",
     *     description="ID категории",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     type="string",
     *     description="Имя товара",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     type="string",
     *     description="Описание товара",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="price",
     *     in="formData",
     *     type="number",
     *     description="Цена",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="size",
     *     in="formData",
     *     type="string",
     *     description="Размер",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="composition",
     *     in="formData",
     *     type="string",
     *     description="Состав",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="manufacturer",
     *     in="formData",
     *     type="string",
     *     description="Производитель",
     *     required=false
     * )
     * @SWG\Parameter(
     *     name="photos",
     *     in="formData",
     *     type="array",
     *     @SWG\Items(type="string"),
     *     description="Фотографии",
     *     required=true
     * )
     * @Security(name="Bearer")
     * @SWG\Tag(name="product")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
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
        $product->setDateUpdate(new \DateTime());
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

        // валидация
        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return $this->json([
                'status' => 'error',
                'message' => (string)$errors,
            ], 400);
        }

        $manager->persist($product);
        $manager->flush();

        return $this->json($product, 201, [], ['groups' => ['product']]);
    }
}
