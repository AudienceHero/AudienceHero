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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * RightfullyOwnedValidator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class RightfullyOwnedValidator extends ConstraintValidator
{
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value || !is_object($value)) {
            return;
        }

        if (!$value instanceof OwnableInterface) {
            return;
        }

        if ($this->authorizationChecker->isGranted(OwnableVoter::ATTRIBUTE, $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
                      ->addViolation();
    }
}
