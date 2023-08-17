<?php

namespace App\Controller;


use App\Repository\RecipeRepository;
use App\Entity\SearchData;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET', 'POST'])]
    public function index(
        RecipeRepository $recipeRepository,
        Request $request,
        SearchData $search,
    ): Response {

        $form = $this->createForm(SearchFormType::class);


        $form->handleRequest($request);

        $results = [];
        if ($form->isSubmitted()) {


            $search->setPage($request->query->getInt('page', 1));
            $search->setName($form->get('name')->getData());

            $recipes = $recipeRepository->findBySearch($search);

            return $this->render('pages/recipe/index.html.twig', [
                'formSearch' => $form->createView(),
                'recipes' => $recipes,
                'results' => $results

            ]);
        }


        return $this->render('/pages/home.html.twig', [
            'formSearch' => $form->createView(),
            'recipes' => $recipeRepository->findPublicRecipe(3),
            'results' => $results
        ]);
    }
}
