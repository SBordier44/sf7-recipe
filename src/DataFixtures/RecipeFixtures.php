<?php

namespace App\DataFixtures;

use AllowDynamicProperties;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Xvladqt\Faker\LoremFlickrProvider;

#[AllowDynamicProperties]
class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
        $this->faker = Factory::create('fr_FR');
        $this->faker->addProvider(new LoremFlickrProvider($this->faker));
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $ingredients = array_map(
            static fn(string $name) => (new Ingredient())->setName($name),
            [
                'Tomato',
                'Onion',
                'Garlic',
                'Pasta',
                'Rice',
                'Potato',
                'Carrot',
                'Beef',
                'Chicken',
                'Pork',
                'Fish',
                'Egg',
                'Milk',
                'Butter',
                'Flour',
                'Sugar',
                'Salt',
                'Pepper',
                'Olive oil',
                'Soy sauce',
                'Curry',
                'Paprika',
                'Cumin',
                'Coriander',
                'Cinnamon',
                'Ginger',
                'Vanilla',
                'Lemon',
                'Orange',
                'Apple',
                'Banana',
                'Strawberry',
                'Blueberry',
                'Raspberry',
                'Pineapple',
                'Mango',
                'Kiwi',
                'Avocado',
                'Coconut',
                'Peanut',
                'Almond',
                'Hazelnut',
                'Walnut',
                'Pistachio',
                'Cashew',
                'Chestnut',
                'Pecan',
                'Macadamia'
            ]
        );

        $units = ['g', 'kg', 'ml', 'cl', 'l', 'piece'];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        for ($i = 0; $i < 50; $i++) {
            /** @var User $user */
            $user = $this->getReference(array_rand($this->referenceRepository->getReferencesByClass()[User::class]));
            $recipeData = $this->getRecipe();
            $recipe = (new Recipe())
                ->setTitle($recipeData['strMeal'])
                ->setCategory($this->getReference('category_' . $recipeData['strCategory']))
                ->setDuration(rand(5, 120))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-6 months')))
                ->setContent($recipeData['strInstructions'])
                ->setThumbnail($this->getRecipeThumbnail($recipeData))
                ->setOwner($user);

            foreach ($this->faker->randomElements($ingredients, rand(2, 5)) as $ingredient) {
                $recipe->addQuantity(
                    (new Quantity())
                        ->setIngredient($ingredient)
                        ->setUnit($this->faker->randomElement($units))
                        ->setQuantity($this->faker->numberBetween(1, 250))
                );
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    private function getRecipe(): array
    {
        $data = $this->httpClient->request('GET', 'https://www.themealdb.com/api/json/v1/1/random.php');
        return $data->toArray()['meals'][0];
    }

    private function getRecipeThumbnail(array $recipeData): string
    {
        $thumbnail = $recipeData['strMealThumb'];
        $path = 'public/images/recipes/thumbnail/' . basename($thumbnail);
        $fileHandler = fopen($path, 'wb');
        $response = $this->httpClient->request('GET', $thumbnail);
        foreach ($this->httpClient->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }
        fclose($fileHandler);
        return basename($thumbnail);
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, UserFixtures::class, AppFixtures::class];
    }
}
