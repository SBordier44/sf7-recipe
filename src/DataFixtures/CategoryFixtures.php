<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getRecipeCategories() as $categoryData) {
            $category = (new Category())
                ->setName($categoryData['strCategory']);

            $manager->persist($category);
            $manager->flush();

            $this->addReference('category_' . $categoryData['strCategory'], $category);
        }
    }

    private function getRecipeCategories(): array
    {
        $data = $this->httpClient->request('GET', 'https://www.themealdb.com/api/json/v1/1/categories.php');
        return $data->toArray()['categories'];
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [AppFixtures::class];
    }
}
