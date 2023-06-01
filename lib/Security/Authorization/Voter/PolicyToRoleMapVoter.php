<?php

declare(strict_types=1);

namespace Netgen\Layouts\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function is_string;
use function str_starts_with;

/**
 * Votes on Netgen Layouts permissions (nglayouts:*) by mapping the permissions to built-in roles (ROLE_NGLAYOUTS_*).
 *
 * @extends \Symfony\Component\Security\Core\Authorization\Voter\Voter<string, mixed>
 */
final class PolicyToRoleMapVoter extends Voter
{
    /**
     * Map of supported permissions to their respective roles.
     */
    private const POLICY_TO_ROLE_MAP = [
        'nglayouts:block:add' => self::ROLE_EDITOR,
        'nglayouts:block:edit' => self::ROLE_EDITOR,
        'nglayouts:block:delete' => self::ROLE_EDITOR,
        'nglayouts:block:reorder' => self::ROLE_EDITOR,
        'nglayouts:layout:add' => self::ROLE_ADMIN,
        'nglayouts:layout:edit' => self::ROLE_EDITOR,
        'nglayouts:layout:delete' => self::ROLE_ADMIN,
        'nglayouts:layout:clear_cache' => self::ROLE_ADMIN,
        'nglayouts:mapping:edit' => self::ROLE_ADMIN,
        'nglayouts:mapping:edit_group' => self::ROLE_ADMIN,
        'nglayouts:mapping:activate' => self::ROLE_ADMIN,
        'nglayouts:mapping:activate_group' => self::ROLE_ADMIN,
        'nglayouts:mapping:delete' => self::ROLE_ADMIN,
        'nglayouts:mapping:reorder' => self::ROLE_ADMIN,
        'nglayouts:collection:edit' => self::ROLE_EDITOR,
        'nglayouts:collection:items' => self::ROLE_EDITOR,
        'nglayouts:ui:access' => self::ROLE_ADMIN,
        'nglayouts:api:read' => self::ROLE_API,
    ];

    /**
     * The identifier of the admin role. Users having this role
     * have full and unrestricted access to the entire system.
     */
    private const ROLE_ADMIN = 'ROLE_NGLAYOUTS_ADMIN';

    /**
     * The identifier of the editor role. Users having this role
     * have full access only to the layout editing interface.
     */
    private const ROLE_EDITOR = 'ROLE_NGLAYOUTS_EDITOR';

    /**
     * The identifier of the API role. Users having this role
     * have access to read only data of the API endpoints.
     */
    private const ROLE_API = 'ROLE_NGLAYOUTS_API';

    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @param mixed $attribute
     * @param mixed $subject
     */
    protected function supports($attribute, $subject): bool
    {
        return is_string($attribute) && str_starts_with($attribute, 'nglayouts:');
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!isset(self::POLICY_TO_ROLE_MAP[$attribute])) {
            return false;
        }

        return $this->accessDecisionManager->decide(
            $token,
            [self::POLICY_TO_ROLE_MAP[$attribute]],
            $subject,
        );
    }
}
