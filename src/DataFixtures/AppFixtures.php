<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 9; ++$i) {
            $category = new Category();
            $category->setName('Категория '.$i);

            $product1 = $this->makeProduct($category);
            $product2 = $this->makeProduct($category);

            $category->setProducts(new ArrayCollection([
                $product1
                    ->setPhotos(new ArrayCollection([
                        $this->makePhoto($product1),
                        $this->makePhoto($product1),
                        $this->makePhoto($product1),
                        $this->makePhoto($product1),
                    ])),
                $product2
                    ->setPhotos(new ArrayCollection([
                        $this->makePhoto($product2),
                    ])),
            ]));

            $manager->persist($category);
        }

        $category = new Category();
        $category->setName('Категория пустая');
        $manager->persist($category);

        $manager->flush();
    }

    private function getRandomPhotoPath(): string
    {
        $files = \array_diff(\scandir(__DIR__.'/images', \SCANDIR_SORT_NONE), ['..', '.']);

        return $files[\array_rand($files, 1)];
    }

    private function makePhoto(Product $product): Photo
    {
        $photoSourcePath = $this->getRandomPhotoPath();
        $photoDestPath = \mt_rand(1000, 999999).'_'.$this->getRandomPhotoPath();

        @\mkdir(__DIR__.'/../../public/upload/2018-01-01', 0777);
        \copy(__DIR__.'/images/'.$photoSourcePath, __DIR__.'/../../public/upload/2018-01-01/'.$photoDestPath);

        return (new Photo())
            ->setProduct($product)
            ->setDateCreate(new \DateTime())
            ->setPath('/upload/2018-01-01/'.$photoDestPath);
    }

    private function makeProduct(Category $category): Product
    {
        return (new Product())
            ->setCategory($category)
            ->setName('Продукт '.\uniqid('', true))
            ->setDateCreate(new \DateTime())
            ->setDescription('Описание товара '.\uniqid('', true))
            ->setPrice(\round(\mt_rand(10, 1000) / \mt_rand(1, 10), 2))
            ->setComposition('composition')
            ->setManufacturer('manufacturer')
            ->setSize('size');
    }
}
