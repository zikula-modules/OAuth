<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Zikula\OAuthModule\Entity\MappingEntity;

class MappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MappingEntity::class);
    }

    public function getZikulaId($method, $methodId)
    {
        $mapping = parent::findOneBy(['method' => $method, 'methodId' => $methodId]);

        return isset($mapping) ? $mapping->getZikulaId() : null;
    }

    public function persistAndFlush(MappingEntity $entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush($entity);
    }

    public function removeByZikulaId($uid)
    {
        $mapping = parent::findOneBy(['zikulaId' => $uid]);
        if (isset($mapping)) {
            $this->_em->remove($mapping);
            $this->_em->flush();
        }
    }
}
