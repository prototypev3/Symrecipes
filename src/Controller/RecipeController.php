<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\SearchData;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Form\SearchFormType;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param RecipeRepository $recipeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $recipeRepository,
        PaginatorInterface $paginator,
        Request $request,
        SearchData $search
    ): Response {
        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        $formSearch = $this->createForm(SearchFormType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted()) {

            $search->setPage($request->query->getInt('page', 1));
            $search->setName($formSearch->get('name')->getData());
            $search->setIdUser($this->getUser()->getId());

            $recipes = $recipeRepository->findBySearchAndUserId($search); // faire cette fonction 

            return $this->render('pages/recipe/index.html.twig', [
                'formSearch' => $formSearch->createView(),
                'recipes' => $recipes

            ]);
        }


        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
            'formSearch' => $formSearch
        ]);
    }

    #[Route('/recette/communaute', 'recipe.community', methods: ['GET'])]
    public function indexPublic(
        RecipeRepository $recipeRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $cache = new FilesystemAdapter();
        $data = $cache->get('recipes', function (ItemInterface $item) use ($recipeRepository) {
            $item->expiresAfter(15);
            return $recipeRepository->findPublicRecipe(null);
        });

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/community.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * This controller allow us to create a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $recipe->setUser($this->getUser());

            if (array_key_exists('isFavorite', $request->request->all()['recipe'])) {
                $recipe->addUsersFavorite($this->getUser());
            }


            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été crée avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow us to edit a recipe
     *
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    #[IsGranted(
        attribute: new Expression('user === subject'),
        subject: new Expression('args["recipe"].getUser()'),
        statusCode: 401,
        message: "Vous n'êtes pas autorisé à modifier cet élément."
    )]
    public function edit(
        RecipeRepository $recipeRepository,
        Request $request,
        EntityManagerInterface $manager,
        Recipe $recipe,
        int $id,
    ): Response {
        $recipe = $recipeRepository->findOneBy(["id" => $id]);
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!array_key_exists('isFavorite', $request->request->all()['recipe'])) {
                $recipe->removeUsersFavorite($this->getUser());
            } else {
                $recipe->addUsersFavorite($this->getUser());
            }

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow us to delete a recipe
     *
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recette/suppression/{id}', 'recipe.delete', methods: ['GET'])]
    #[IsGranted(
        attribute: new Expression('user === subject'),
        subject: new Expression('args["recipe"].getUser()'),
        statusCode: 401,
        message: "Vous n'êtes pas autorisé à supprimer cet élément."
    )]
    public function delete(
        EntityManagerInterface $manager,
        RecipeRepository $recipeRepository,
        Recipe $recipe,
        int $id
    ): Response {
        $recipe = $recipeRepository->findOneBy(["id" => $id]);
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !'
        );

        return $this->redirectToRoute('recipe.index');
    }

    /**
     * This controller allow us to see a recipe if this one is public
     *
     * @param Recipe $recipe
     * @return Response
     */

    #[Route('/recette/{id}', 'recipe.show', methods: ['GET', 'POST'])]

    public function show(
        Recipe $recipe,
        Request $request,
        MarkRepository $markRepository,
        EntityManagerInterface $manager
    ): Response {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe
            ]);

            if (!$existingMark) {
                $manager->persist($mark);
            } else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte.'
            );

            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }
}
