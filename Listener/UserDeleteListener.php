<?php

declare(strict_types=1);

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\OAuthModule\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\UsersModule\Event\ActiveUserPostDeletedEvent;
use Zikula\UsersModule\Event\RegistrationPostDeletedEvent;
use Zikula\UsersModule\Event\UserEntityEvent;

class UserDeleteListener implements EventSubscriberInterface
{
    /**
     * @var MappingRepository
     */
    private $mappingRepository;

    public function __construct(MappingRepository $mappingRepository)
    {
        $this->mappingRepository = $mappingRepository;
    }

    public function deleteUser(UserEntityEvent $event): void
    {
        $deletedUid = $event->getUser()->getUid();
        $this->mappingRepository->removeByZikulaId($deletedUid);
    }

    public static function getSubscribedEvents()
    {
        return [
            ActiveUserPostDeletedEvent::class => [
                'deleteUser'
            ],
            RegistrationPostDeletedEvent::class => [
                'deleteUser'
            ]
        ];
    }
}
