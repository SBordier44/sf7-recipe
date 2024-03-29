<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RecipeVoter extends Voter
{
    public const string EDIT = 'RECIPE_EDIT';
    public const string VIEW = 'RECIPE_VIEW';
    public const string CREATE = 'RECIPE_CREATE';
    public const string DELETE = 'RECIPE_DELETE';
    public const string LIST = 'RECIPE_LIST';
    public const string LIST_ALL = 'RECIPE_LIST_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
            in_array($attribute, [self::LIST, self::CREATE, self::LIST_ALL]) ||
            (
                in_array($attribute, [self::EDIT, self::VIEW, self::DELETE]) && $subject instanceof Recipe
            );
    }

    /**
     * @param string $attribute
     * @param Recipe $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EDIT, self::DELETE, => $subject->getOwner()?->getId() === $user->getId(),
            self::CREATE, self::LIST, self::VIEW => true,
            default => false,
        };
    }
}
