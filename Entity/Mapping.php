<?php
/**
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class Mapping
 * @ORM\Entity(repositoryClass="Zikula\OAuthModule\Entity\Repository\MappingRepository")
 * @ORM\Table(name="oauth_mapping")
 */
class Mapping
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

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethodId()
    {
        return $this->methodId;
    }

    /**
     * @param string $methodId
     */
    public function setMethodId($methodId)
    {
        $this->methodId = $methodId;
    }

    /**
     * @return integer
     */
    public function getZikulaId()
    {
        return $this->zikulaId;
    }

    /**
     * @param integer $zikulaId
     */
    public function setZikulaId($zikulaId)
    {
        $this->zikulaId = $zikulaId;
    }
}
