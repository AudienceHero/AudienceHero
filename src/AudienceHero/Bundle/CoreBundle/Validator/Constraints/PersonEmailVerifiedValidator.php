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

namespace AudienceHero\Bundle\CoreBundle\Validator\Constraints;

use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * PersonEmailVerifiedValidator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PersonEmailVerifiedValidator extends ConstraintValidator
{
    /**
     * @param PersonEmail $value
     * @param Constraint  $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        Assert::object($value, 'Value %s should be of type Resources');
        Assert::isInstanceOf($value, PersonEmail::class, 'Value %s should be of type Resources');

        /** var Resources $value */
        if ($value->getIsVerified()) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
