<?php

namespace App\Controller;

use App\Document\Category;
use App\Document\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrivateController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function userRefreshAction(): JsonResponse
    {
        return $this->json([
            'status' => 'success'
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function userAction(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'data' => [
                'login' => $this->getParameter('login')
            ]
        ]);
    }


    /**
     * @param Request $request
     * @return bool
     */
    protected function isAuthenticated(Request $request): bool
    {
        return $request->headers->get('PHP_AUTH_USER') === $this->getParameter('login') &&
            $request->headers->get('PHP_AUTH_PW') === $this->getParameter('password');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addCategoryAction(Request $request): JsonResponse
    {
        $categoryName = $request->request->get('categoryName');
        if (null === $categoryName || '' === $categoryName) {
            throw new \InvalidArgumentException('Не указано название категории');
        }

        $manager = $this->get('doctrine_mongodb')->getManager();

        $category = new Category();
        $category->setName($categoryName);

        $manager->persist($category);
        $manager->flush();

        return $this->json($category, 200, [], ['groups' => ['categories']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteProductAction(Request $request): JsonResponse
    {
        $productId = $request->request->get('productId');
        if (null === $productId || '' === $productId) {
            throw new \InvalidArgumentException('Не указан идентификатор товара');
        }

        $manager = $this->get('doctrine_mongodb')->getManager();
        $repository = $manager->getRepository(Product::class);

        /** @var Product $product */
        $product = $repository->find($productId);
        if (null === $product) {
            $this->createNotFoundException('Товар не найден');
        }

        $manager->remove($product);
        $manager->flush();

        return $this->json(null);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCategoryAction(Request $request): JsonResponse
    {
        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }

        $manager = $this->get('doctrine_mongodb')->getManager();
        $repository = $manager->getRepository(Category::class);

        /** @var Category $category */
        $category = $repository->find($categoryId);
        if (null === $category) {
            $this->createNotFoundException('Категория не найдена');
        }

        $manager->remove($category);
        $manager->flush();

        return $this->json(null);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCategoryAction(Request $request): JsonResponse
    {
        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }
        $categoryName = $request->request->get('categoryName');
        if (null === $categoryName || '' === $categoryName) {
            throw new \InvalidArgumentException('Не указано название категории');
        }

        $manager = $this->get('doctrine_mongodb')->getManager();
        $repository = $manager->getRepository(Category::class);

        /** @var Category $category */
        $category = $repository->find($categoryId);
        if (null === $category) {
            $this->createNotFoundException('Категория не найдена');
        }

        $category->setDateUpdate(new \DateTime());
        $category->setName($categoryName);

        $manager->persist($category);
        $manager->flush();

        return $this->json($category, 200, [], ['groups' => ['categories']]);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return JsonResponse|mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __call(string $method, array $arguments)
    {
        $request = $arguments[0];

        if (!$this->isAuthenticated($request)) {
            $this->get('logger')->error('Попытка войти в админку с некорректным токеном.', [$request]);

            return $this->json([
                'status' => 'error',
                'msg' => 'Invalid Credentials'
            ], Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Basic realm="Authorization"']);
        }

        if (\method_exists($this, $method)) {
            return \call_user_func_array([$this, $method], $arguments);
        }

        throw $this->createNotFoundException();
    }
}
