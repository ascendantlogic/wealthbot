<?php

namespace Wealthbot\ClientBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Wealthbot\ClientBundle\Entity\AccountGroup;
use Wealthbot\ClientBundle\Entity\Distribution;
use Wealthbot\ClientBundle\Entity\SystemAccount;
use Wealthbot\UserBundle\Entity\User;

/**
 * SystemAccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SystemAccountRepository extends EntityRepository
{
    public function findByClientIdAndType($clientId, $type)
    {
        $qb = $this->createQueryBuilder('sa');

        $qb->where('sa.client_id = :client_id')
            ->andWhere('sa.type = :type')
            ->andWhere('sa.status != :status')
            ->setParameters(array(
                'client_id' => $clientId,
                'type' => $type,
                'status' => SystemAccount::STATUS_CLOSED
            ));

        return $qb->getQuery()->getResult();
    }

    public function findByClientIdAndNotType($clientId, $type)
    {
        $qb = $this->createQueryBuilder('sa');

        $qb->where('sa.client_id = :client_id')
            ->andWhere('sa.type != :type')
            ->andWhere('sa.status != :status')
            ->setParameters(array(
                'client_id' => $clientId,
                'type' => $type,
                'status' => SystemAccount::STATUS_CLOSED
            ));

        return $qb->getQuery()->getResult();
    }

    public function findRetirementByClientId($clientId)
    {
        return $this->findByClientIdAndType($clientId, SystemAccount::TYPE_RETIREMENT);
    }

    public function findNotRetirementByClientId($clientId)
    {
        return $this->findByClientIdAndNotType($clientId, SystemAccount::TYPE_RETIREMENT);
    }

    public function findContributionDistributionAccounts(User $client)
    {
        $qb = $this->createQueryBuilder('sa');
        $subSql = '(SELECT 1 FROM Wealthbot\ClientBundle\Entity\Distribution sad
                    WHERE sad.type = :distribution_type AND sad.systemClientAccount = sa)';

        $qb->select('sa as account, ' . $subSql . ' as has_scheduled_distribution')
            ->where('sa.client = :client')
            ->andWhere('sa.type != :type')
            ->andWhere('sa.status != :status')
            ->setParameters(array(
                'client' => $client,
                'type' => SystemAccount::TYPE_RETIREMENT,
                'status' => SystemAccount::STATUS_CLOSED,
                'distribution_type' => Distribution::TYPE_SCHEDULED
            ));

        return $qb->getQuery()->getResult();
    }

    public function findOneRetirementAccountById($id)
    {
        $qb = $this->createQueryBuilder('sa');

        $qb->where('sa.id = :id')
            ->andWhere('sa.type = :type')
            ->setParameters(array(
                'id' => $id,
                'type' => SystemAccount::TYPE_RETIREMENT
            ))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    // TODO: May require refactoring
    public function findWithBeneficiariesByClientId($clientId)
    {
        $qb = $this->createQueryBuilder('sa');

        $qb->leftJoin('sa.clientAccount', 'ca')
            ->leftJoin('ca.consolidatedAccounts', 'c_ca')
            ->leftJoin('ca.groupType', 'gt')
            ->leftJoin('gt.group', 'g')
            ->leftJoin('c_ca.groupType', 'c_gt')
            ->leftJoin('c_gt.group', 'c_g')
            ->where('sa.status != :status')
            ->andWhere('ca.client_id = :client_id')
            ->andWhere('(sa.type = :type_roth OR sa.type = :type_traditional OR g.name = :group_rollover OR c_g.name = :group_rollover)')
            ->setParameters(array(
                'status' => SystemAccount::STATUS_CLOSED,
                'client_id'        => $clientId,
                'group_rollover'   => AccountGroup::GROUP_OLD_EMPLOYER_RETIREMENT,
                'type_roth'        => SystemAccount::TYPE_ROTH_IRA,
                'type_traditional' => SystemAccount::TYPE_TRADITIONAL_IRA
            ));

        return $qb->getQuery()->getResult();
    }

    public function findByClientIdAndNotStatus($clientId, $status)
    {
        $qb = $this->createQueryBuilder('sa');

        $qb->where('sa.client_id = :client_id')
            ->andWhere('sa.status != :status')
            ->setParameters(array(
                'client_id' => $clientId,
                'status' => $status
            ));

        return $qb->getQuery()->getResult();
    }

    public function findByClientId($clientId)
    {
        return $this->findByClientIdAndNotStatus($clientId, SystemAccount::STATUS_CLOSED);
    }

    public function isClientAccounts($clientId, array $ids)
    {
        $qb = $this->createQueryBuilder('sa');

        $notClientAccount = $qb->where('sa.client_id != :client_id')
            ->andWhere($qb->expr()->in('sa.id', $ids))
            ->setParameter('client_id', $clientId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $notClientAccount ? false : true;
    }

    public function findWhereIdIn($ids)
    {
        $qb = $this->createQueryBuilder('sa');
        $qb->where($qb->expr()->in('sa.id', $ids));

        return $qb->getQuery()->execute();
    }

    public function findByAccountNumber($accountNumber)
    {
        $qb = $this->createQueryBuilder('sa');
        $qb->where('sa.account_number = :account_number')
            ->setParameter('account_number', $accountNumber)
            ->setMaxResults(1);

        return $qb->getQuery()->getResult();
    }

    public function findNotClosed(User $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.status <> :status')
            ->andWhere('a.client = :user')
            ->setParameter('status', SystemAccount::STATUS_CLOSED)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findHasActivityByPeriod(User $client, \DateTime $dateFrom, \DateTime $dateTo)
    {
        return $this->createQueryBuilder('s')
            ->where('s.client = :client')
            ->setParameter('client', $client)
            ->leftJoin('s.clientAccountValues', 'av')
            ->andWhere('av.date >= :dateFrom')
            ->andWhere('av.dateTo < :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Getting all not activated accounts has waiting time too long.
     *
     * dateForTransfer - current date minus Maximum Days for activating transfer and rollover accounts.
     * dateForNew - current date minus Maximum Days for activating new accounts.
     *
     * @param \DateTime $dateForTransfer
     * @param \DateTime $dateForNew
     * @return SystemAccount[]
     */
    public function getMustBeAcceptedAlready(\DateTime $dateForTransfer, \DateTime $dateForNew)
    {
        $qb = $this->createQueryBuilder('sa')
            ->join('sa.client', 'c')
            ->join('c.clientPortfolios', 'p')
            ->where('(p.is_active = true) AND (p.approved_at < :newAccountDate) AND (sa.creationType = :creationTypeNew) AND (sa.status = :waitingStatus)')
            ->orWhere('(p.is_active = true) AND (p.approved_at < :transferAccountDate) AND (sa.creationType IN (:creationTypeTransfer)) AND (sa.status = :waitingStatus)')

            ->setParameter('waitingStatus', SystemAccount::STATUS_WAITING_ACTIVATION)
            ->setParameter('creationTypeTransfer', array(SystemAccount::CREATION_TYPE_ROLLOVER_ACCOUNT, SystemAccount::CREATION_TYPE_TRANSFER_ACCOUNT))
            ->setParameter('creationTypeNew', SystemAccount::CREATION_TYPE_NEW_ACCOUNT)
            ->setParameter('transferAccountDate', $dateForTransfer)
            ->setParameter('newAccountDate', $dateForNew)

            ->getQuery();

        return $qb->getResult();

    }

    /**
     * @param null $number
     * @return array
     */
    public function getAll($number = null)
    {
        $qb = $this->createQueryBuilder('sa');

        if (!empty($number)) {
            $qb->where('sa.account_number LIKE :number')->setParameter('number', "%{$number}%");
        }

        return $qb
            ->orderBy('sa.account_number', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
