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

namespace Zikula\OAuthModule\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Bundle\CoreBundle\Event\GenericEvent;
use Zikula\OAuthModule\Entity\Repository\MappingRepository;
use Zikula\UsersModule\Event\DeletedRegistrationEvent;
use Zikula\UsersModule\UserEvents;

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

    public function deleteUser(GenericEvent $event): void
    {
        $deletedUid = $event->getSubject();
        $this->mappingRepository->removeByZikulaId($deletedUid);
    }

    public function deleteRegistration(DeletedRegistrationEvent $event): void
    {
        $deletedUid = $event->getUser()->getUid();
        $this->mappingRepository->removeByZikulaId($deletedUid);
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvents::DELETE_ACCOUNT => [
                'deleteUser'
            ],
            DeletedRegistrationEvent::class => [
                'deleteRegistration'
            ]
        ];
    }
}
