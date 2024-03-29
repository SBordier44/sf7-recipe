<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Security\Voter\RecipeVoter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

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
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
        private readonly Security $security
    ) {
        parent::__construct($registry, Recipe::class);
    }

    public function paginateRecipes(int $page, int $limit): PaginationInterface
    {
        $canListAll = $this->security->isGranted(RecipeVoter::LIST_ALL);

        $builder = $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c');

        if (!$canListAll) {
            $builder->where('r.owner = :user')
                ->setParameter('user', $this->security->getUser()?->getId());
        }

        return $this->paginator->paginate(
            $builder->getQuery()
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class)
                ->setHint(TranslatableListener::HINT_FALLBACK, 1),
            $page,
            $limit,
            [
                PaginatorInterface::DISTINCT => false,
                PaginatorInterface::SORT_FIELD_ALLOW_LIST => ['r.title', 'c.name'],
            ]
        );
    }

    public function findTotalDuration(): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Recipe[]
     */
    public function findAllWithJoins(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
