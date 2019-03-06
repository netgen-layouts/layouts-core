<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Security\Authorization\Voter;

use Netgen\BlockManager\Exception\Security\PolicyException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on Netgen Layouts permissions (nglayouts:*) by mapping the permissions to built-in roles (ROLE_NGBM_*).
 */
final class PolicyToRoleMapVoter extends Voter
{
    /**
     * Map of supported permissions to their respective roles.
     */
    private const POLICY_TO_ROLE_MAP = [
        'nglayouts:layout:add' => self::ROLE_ADMIN,
        'nglayouts:block:add' => self::ROLE_EDITOR,
    ];

    /**
     * The identifier of the admin role. Users having this role
     * have full and unrestricted access to the entire system.
     */
    private const ROLE_ADMIN = 'ROLE_NGBM_ADMIN';

    /**
     * The identifier of the editor role. Users having this role
     * have full access only to the layout editing interface.
     */
    private const ROLE_EDITOR = 'ROLE_NGBM_EDITOR';

    /**
     * @var \Symfony\Component\Security\Core\Security|\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $security;

    /**
     * @deprecated Injecting AuthorizationCheckerInterface is deprecated since it leads to circular
     * reference exceptions in Symfony >= 4.0. Remove when support for Symfony 2.8 ends.
     *
     * @param \Symfony\Component\Security\Core\Security|\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $security
     */
    public function __construct($security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return is_string($attribute) && mb_strpos($attribute, 'nglayouts:') === 0;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!isset(self::POLICY_TO_ROLE_MAP[$attribute])) {
            throw PolicyException::policyNotSupported($attribute);
        }

        return $this->security->isGranted(
            self::POLICY_TO_ROLE_MAP[$attribute],
            $subject
        );
    }
}
