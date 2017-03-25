<?php
namespace Hack2Hire\FraudDetectionBackend\Repositories;

use Doctrine\ORM\EntityRepository;
use Hack2Hire\FraudDetectionBackend\Entities\Transaction;

/**
 * @method Transaction findOneBy(array $criteria)
 * @method Transaction[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($obj)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function save($obj)
    {
        $em = $this->getEntityManager();
        $em->flush($obj);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($obj)
    {
        $em = $this->getEntityManager();
        $em->remove($obj);
        $em->flush();
    }
}