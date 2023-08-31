<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Entity\User;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symrecipe - Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Home');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);

        yield MenuItem::section('Ingredients');
        yield MenuItem::linkToCrud('Ingrédients', 'fas fa-receipt', Ingredient::class);

        yield MenuItem::section('Recipes');
        yield MenuItem::linkToCrud('Recettes', 'fas fa-bowl-food', Recipe::class);

        yield MenuItem::section('Contact');
        yield MenuItem::linkToCrud('Demandes de contact', 'fas fa-envelope', Contact::class);
    }
}
