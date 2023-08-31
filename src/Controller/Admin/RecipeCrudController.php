<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Recette')
            ->setEntityLabelInPlural('Recettes')

            ->setPageTitle("index", "SymRecipe - Administration des recettes")

            ->setPaginatorPageSize(20)

            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }


    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        return [
            AssociationField::new('user'),
            TextField::new('name'),
            EmailField::new('email')
                ->hideOnform(),
            TextField::new('description')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex(),
            MoneyField::new('price', 'Prix')
                ->setCurrency('EUR')
                ->setCustomOption('storedAsCents', false),
            DateTimeField::new('createdAt')
                ->hideOnForm()
        ];
    }
}
