<?php

namespace App\Controller\Api;

use App\Dto\PaginationDto;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    #[Route('/', methods: ['GET'])]
    public function index(
        RecipeRepository $recipeRepository,
        #[MapQueryString] ?PaginationDto $paginationDto
    ): JsonResponse {
        return $this->json(
            $recipeRepository->paginateRecipes($paginationDto->page ?? 1, 10),
            200,
            [],
            ['groups' => 'recipes.index']
        );
    }

    #[Route('/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Recipe $recipe): JsonResponse
    {
        return $this->json(
            $recipe,
            200,
            [],
            ['groups' => 'recipes.show']
        );
    }

    #[Route('/', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(serializationContext: ['groups' => 'recipes.create'])] Recipe $recipe,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $entityManager->persist($recipe);
        $entityManager->flush();

        return $this->json(
            $recipe,
            201,
            [],
            ['groups' => 'recipes.show']
        );
    }
}
