<?php

namespace AppBundle\Twig;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class EasyAdminExtension extends \Twig_Extension
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'filter_admin_actions',
                [$this, 'filterActions']
            )
        ];
    }

    public function filterActions(array $itemActions, $item)
    {
        echo 'test';

        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            unset($itemActions['admin_impersonate_user']);
        }

        return $itemActions;
    }
}
