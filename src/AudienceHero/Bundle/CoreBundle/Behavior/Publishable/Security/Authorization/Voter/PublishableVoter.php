<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Security\Authorization\Voter;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PublishableVoter extends Voter
{
    public const SEE = 'FRONT_SEE';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if (self::SEE !== $attribute) {
            return false;
        }

        if (!$subject instanceof PublishableInterface) {
            return false;
        }

        return true;
    }

    /**
     * @param $attribute
     * @param PublishableInterface $subject
     * @param TokenInterface $token
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($subject->isPrivacyPublic()) {
            return true;
        }

        if ($subject->isPrivacyUnlisted()) {
            return true;
        }

        // At this point, we need an user to be sure.
        if ($token instanceof AnonymousToken) {
            return false;
        }

        if (!$subject instanceof OwnableInterface) {
            throw new \LogicException(sprintf('At this point, class %s should implements OwnableInterface', get_class($subject)));
        }

        /** @var OwnableInterface $owner */
        $owner = $subject->getOwner();
        if (!$owner) {
            throw new \LogicException('Object should have an owner.');
        }

        $user = $token->getUser();
        if (!$user) {
            throw new \LogicException('Token should contain a user');
        }

        if ($user->getId() === $owner->getId()) {
            return true;
        }

        return false;
    }
}
