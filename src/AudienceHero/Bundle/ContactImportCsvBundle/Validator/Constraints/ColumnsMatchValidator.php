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

namespace AudienceHero\Bundle\ContactImportCsvBundle\Validator\Constraints;

use AudienceHero\Bundle\ContactImportCsvBundle\CSV\ColumnsMatcher;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * ColumnsMatchValidator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ColumnsMatchValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($matches, Constraint $constraint)
    {
        if (!$constraint instanceof ColumnsMatch) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ColumnsMatch');
        }

        // Make sure columns can be matched once
        $uniq = [];
        foreach ($matches as $key => $value) {
            if (ColumnsMatcher::COLUMN_SKIP === $value) {
                continue;
            }

            if (isset($uniq[$value])) {
                $this->context->buildViolation($constraint->messageColumnsCanOnlyBeMatchedOnce)
                    ->atPath($key)
                    ->setCode(ColumnsMatch::COLUMNS_CAN_ONLY_BE_MATCHED_ONCE)
                    ->addViolation();

                $this->context->buildViolation($constraint->messageColumnsCanOnlyBeMatchedOnce)
                    ->atPath($uniq[$value])
                    ->setCode(ColumnsMatch::COLUMNS_CAN_ONLY_BE_MATCHED_ONCE)
                    ->addViolation();

                continue;
            }

            $uniq[$value] = $key;
        }

        // Check if there's not conflict with first_name, last_name and full_name
        $founds = [];
        foreach ($matches as $key => $value) {
            if (ColumnsMatcher::COLUMN_FIRST_NAME === $value || ColumnsMatcher::COLUMN_LAST_NAME === $value) {
                $founds[$key] = $value;
                continue;
            }

            if (ColumnsMatcher::COLUMN_FULL_NAME === $value) {
                $founds[$key] = $value;
                continue;
            }
        }

        if (in_array(ColumnsMatcher::COLUMN_FULL_NAME, $founds, true) && (in_array(ColumnsMatcher::COLUMN_LAST_NAME, $founds, true) || in_array(ColumnsMatcher::COLUMN_FIRST_NAME, $founds, true))) {
            foreach ($founds as $key => $value) {
                $this->context->buildViolation($constraint->messageFullNameIncompatibility)
                    ->atPath($key)
                    ->setCode(ColumnsMatch::COLUMNS_FULL_NAME_IS_IMCOMPATIBLE_WITH_FIRST_OR_LAST_NAME)
                    ->addViolation();
            }
        }
    }
}
