<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Manage the creation of the directory for the recipe images
        $filesystem = new Filesystem();
        $recipeImagesDirectory = 'public/images/recipes/thumbnail';
        if ($filesystem->exists($recipeImagesDirectory)) {
            $filesystem->remove($recipeImagesDirectory);
        }
        $filesystem->mkdir($recipeImagesDirectory);
    }
}
