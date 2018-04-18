<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PrivateController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request): JsonResponse
    {
        $jsonRequest = \json_decode($request->getContent());
        $login = $jsonRequest->login;
        $password = $jsonRequest->password;

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

        if ($matches['token'] !== $expectedHash) {
            throw new HttpException(403, 'Ны не авторизованы');
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addCategoryAction(Request $request): JsonResponse
    {
        $this->checkAuth($request);

        $categoryName = $request->request->get('categoryName');
        if (null === $categoryName || '' === $categoryName) {
            throw new \InvalidArgumentException('Не указано название категории');
        }

        $manager = $this->getDoctrine()->getManager();

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
        $this->checkAuth($request);

        $categoryId = $request->request->get('categoryId');
        if (null === $categoryId || '' === $categoryId) {
            throw new \InvalidArgumentException('Не указан идентификатор категории');
        }
        $categoryName = $request->request->get('categoryName');
        if (null === $categoryName || '' === $categoryName) {
            throw new \InvalidArgumentException('Не указано название категории');
        }

        $manager = $this->getDoctrine()->getManager();
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
}
