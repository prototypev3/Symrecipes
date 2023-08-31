<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use Easycorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Ingrédients')
            ->setEntityLabelInSingular('Ingrédient')

            ->setPageTitle("index", "SymRecipe - Administration des ingrédients")

            ->setPaginatorPageSize(20)

            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }
    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        return [
            AssociationField::new('user'),
            TextField::new('name'),
            MoneyField::new('price', 'Prix')
                ->setCurrency('EUR')
                ->setCustomOption('storedAsCents', false),
            DateTimeField::new('createdAt')
                ->hideOnForm()


        ];
    }
}
