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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * OwnableVoter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OwnableVoter extends Voter
{
    const ATTRIBUTE = 'IS_OWNER';

    /**
     * {@inheritdoc}
     */
    public function supports($attribute, $subject)
    {
        if (self::ATTRIBUTE !== $attribute) {
            return false;
        }

        if (!$subject instanceof OwnableInterface) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($token instanceof AnonymousToken) {
            return false;
        }

        $person = $token->getUser();
        if (!$person instanceof Person) {
            throw new \LogicException(sprintf('expected to retrieve a Person instance, got %s', get_class($person)));
        }

        if (null === $subject->getOwner()) {
            throw new \LogicException('object has no owner.');
        }

        if ($subject->getOwner()->getId() === $person->getId()) {
            return true;
        }

        return false;
    }
}
