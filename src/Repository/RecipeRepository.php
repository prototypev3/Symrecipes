<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\SearchData;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * This method allow us to find public recipes based on number of recipes
     *
     * @param integer $nbRecipes
     * @return array
     */
    public function findPublicRecipe(?int $nbRecipes): array
    {
        sleep(3);
        $queryBuilder = $this->createQueryBuilder('r')
            ->where('r.isPublic = 1')
            ->orderBy('r.createdAt', 'DESC');


        if ($nbRecipes !== 0 || $nbRecipes !== null) {
            $queryBuilder->setMaxResults($nbRecipes);
        }

        return $queryBuilder->getQuery()
            ->getResult();
    }


    /**
     * Get published recipes thanks to the Search Data
     *
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findBySearch(SearchData $searchData): array
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.name LIKE :name')
            ->setParameter('name', '%' . $searchData->getName() . '%');

        return $query
            ->getQuery()
            ->getResult();

        // $recipes = $this->$paginatorInterface->paginate($recipes, $searchData->getPage(), 9);
        // return $pagination;
    }

    /**
     * Get published recipes thanks to the Search Data
     *
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findBySearchAndUserId(SearchData $searchData): array
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.name LIKE :name')
            ->andWhere('r.user = :user')
            ->setParameter('name', '%' . $searchData->getName() . '%')
            ->setParameter('user', $searchData->getIdUser());

        return $query
            ->getQuery()
            ->getResult();

        // $recipes = $this->$paginatorInterface->paginate($recipes, $searchData->getPage(), 9);
        // return $pagination;
    }

    /**
     * Get published comments for public recipes
     *
     * @param Comment $comment
     * @return PaginationInterface
     */

    // public function findComment(Comment $comment): array
    // {

    //     $query = $this->createQueryBuilder('c')
    //         ->where('c.name LIKE :comment')
    //         ->andWhere('c.user = :user')
    //         ->setParameter('name', '%' . $comment->getName() . '%')
    //         ->setParameter('user', $comment->getIdUser());


    //     return $query
    //         ->getQuery()
    //         ->getResult();
    // }
}
