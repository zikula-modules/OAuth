<?php

declare(strict_types=1);
/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MappingEntity
 * @ORM\Entity(repositoryClass="Zikula\OAuthModule\Entity\Repository\MappingRepository")
 * @ORM\Table(name="oauth_mapping")
 */
class MappingEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $method;

    /**
     * @ORM\Column(type="string")
     */
    private $methodId;

    /**
     * @ORM\Column(type="integer")
     */
    private $zikulaId;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getMethodId(): string
    {
        return $this->methodId;
    }

    public function setMethodId(string $methodId): void
    {
        $this->methodId = $methodId;
    }

    public function getZikulaId(): int
    {
        return $this->zikulaId;
    }

    public function setZikulaId(int $zikulaId): void
    {
        $this->zikulaId = $zikulaId;
    }
}
