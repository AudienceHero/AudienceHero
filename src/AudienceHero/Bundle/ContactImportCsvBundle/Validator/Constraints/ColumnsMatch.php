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

use Symfony\Component\Validator\Constraint;

/**
 * ColumnsMatch.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ColumnsMatch extends Constraint
{
    public const COLUMNS_CAN_ONLY_BE_MATCHED_ONCE = '578efcde-4042-49d8-bbc1-3fc93a4fb857';
    public const COLUMNS_FULL_NAME_IS_IMCOMPATIBLE_WITH_FIRST_OR_LAST_NAME = '927b677f-9faf-4612-81f1-6c821a1ce5cc';

    protected static $errorNames = [
        self::COLUMNS_CAN_ONLY_BE_MATCHED_ONCE => 'COLUMNS_ARE_MATCHED_MORE_THAN_ONCE_ERROR',
        self::COLUMNS_FULL_NAME_IS_IMCOMPATIBLE_WITH_FIRST_OR_LAST_NAME => 'COLUMNS_INCOMPATIBILITY_ERROR',
    ];

    public $messageColumnsCanOnlyBeMatchedOnce = 'csv_import.violation.columns_can_only_be_matched_once';
    public $messageFullNameIncompatibility = 'csv_import.violation.only_fullname_or_first_name_last_name';
}
