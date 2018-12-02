<?php

namespace App\EventListener;

use App\Entity\Item;
use App\Entity\User;
use App\Event\ItemEvent;
use App\Service\EmailService;
use App\Service\QueryHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class NotificationListener
{

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var QueryHelper
     */
    private $queryHelper;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param EmailService $emailService
     * @param QueryHelper $queryHelper
     */
    public function __construct(TokenStorageInterface $tokenStorage, EmailService $emailService, QueryHelper $queryHelper)
    {
        $this->tokenStorage = $tokenStorage;
        $this->emailService = $emailService;
        $this->queryHelper = $queryHelper;
    }

    /**
     * Authentication handler
     *
     * @param AuthenticationEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User && !$user->getLastLogin()) {
            $this
                ->emailService
                ->sendEmailToUser($user, 'Welcome abroad!', 'emails/registration.html.twig');
        }
    }

    /**
     * @param ItemEvent $event
     */
    public function onItemAdd(ItemEvent $event)
    {
        /** @var Item $item */
        $item = $event->getItem();
        /** @var User $user */
        $user = $item->getUserId();
        $options = array(
            'user_email' => $user->getEmail(),
            'item_name' => $item->getName()
        );

        $admins = $this->queryHelper->getAdminUsers();

        /** @var User $admin */
        foreach ($admins as $admin) {
            $this->emailService->sendEmailToUser($admin, 'New Item has been added!', 'emails/new_item.htm.twig', $options);
        }
    }
}
