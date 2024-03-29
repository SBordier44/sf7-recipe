<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Message\RecipePDFMessage;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/admin/recipe', name: 'admin.recipe.')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted(RecipeVoter::LIST)]
    public function index(RecipeRepository $recipeRepository, Request $request): Response
    {
        $recipes = $recipeRepository->paginateRecipes(
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted(RecipeVoter::CREATE)]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);

            $em->flush();

            $this->addFlash('success', 'La recette a bien été créée.');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'edit', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['GET', 'POST'])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $em,
        MessageBusInterface $messageBus
    ): Response {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $messageBus->dispatch(new RecipePDFMessage($recipe->getId()));

            $this->addFlash('success', 'La recette a bien été modifiée.');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'delete', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['DELETE'])]
    #[IsGranted(RecipeVoter::DELETE, subject: 'recipe')]
    public function remove(Recipe $recipe, EntityManagerInterface $em, Request $request): Response
    {
        $recipeId = $recipe->getId();
        $successMessage = 'La recette a bien été supprimée.';

        $em->remove($recipe);
        $em->flush();

        if ($request->getPreferredFormat() === TurboBundle::STREAM_FORMAT) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('admin/recipe/_delete.html.twig', [
                'recipeId' => $recipeId,
                'successMessage' => $successMessage,
            ]);
        }

        $this->addFlash('success', $successMessage);

        return $this->redirectToRoute('admin.recipe.index');
    }
}
