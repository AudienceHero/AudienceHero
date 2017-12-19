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

namespace AudienceHero\Bundle\ContactImportCsvBundle\CSV;

class ColumnsMatcher
{
    const COLUMN_SKIP = 'skip';
    const COLUMN_FIRST_NAME = 'first_name';
    const COLUMN_LAST_NAME = 'last_name';
    const COLUMN_FULL_NAME = 'full_name';
    const COLUMN_EMAIL = 'email';
    const COLUMN_PHONE = 'phone';
    const COLUMN_CITY = 'city';
    const COLUMN_COUNTRY = 'country';
    const COLUMN_POSTAL_CODE = 'postal_code';
    const COLUMN_ADDRESS = 'address';
    const COLUMN_NOTES = 'notes';
    const COLUMN_SALUTATION_NAME = 'salutation_name';
    const COLUMN_HOMEPAGE = 'homepage';
    const COLUMN_COMPANY_NAME = 'company_name';

    private $columns = [];
    private $matches = [];

    public function __construct(array $columns)
    {
        $this->columns = $columns;
        $this->guessMatches();
    }

    public static function getChoices(): array
    {
        return [
             self::COLUMN_SKIP,
             self::COLUMN_EMAIL,
             self::COLUMN_FULL_NAME,
             self::COLUMN_FIRST_NAME,
             self::COLUMN_LAST_NAME,
             self::COLUMN_SALUTATION_NAME,
             self::COLUMN_ADDRESS,
             self::COLUMN_POSTAL_CODE,
             self::COLUMN_CITY,
             self::COLUMN_COUNTRY,
             self::COLUMN_PHONE,
             self::COLUMN_HOMEPAGE,
             self::COLUMN_COMPANY_NAME,
             self::COLUMN_NOTES,
        ];
    }

    public function __set(int $index, string $value): void
    {
        $this->matches[sprintf('col%02d', $index)] = $value;
    }

    public function __get(string $index)
    {
        $index = sprintf('col%02d', $index);
        if (isset($this->matches[$index])) {
            return $this->matches[$index];
        }

        return null;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getMatches()
    {
        return $this->matches;
    }

    public function setMatches(array $matches)
    {
        $this->matches = $matches;
    }

    public function guessMatches()
    {
        foreach ($this->columns as $key => $header) {
            switch (strtolower($header)) {
            case 'name':
            case 'buyer name':
                $this->__set($key, self::COLUMN_FULL_NAME);
                break;
            case 'firstname':
            case 'first_name':
                $this->__set($key, self::COLUMN_FIRST_NAME);
                break;
            case 'lastname':
            case 'last_name':
                $this->__set($key, self::COLUMN_LAST_NAME);
                break;
            case 'email':
            case 'buyer email':
                $this->__set($key, self::COLUMN_EMAIL);
                break;
            case 'homepage':
                $this->__set($key, self::COLUMN_HOMEPAGE);
                break;
            case 'country':
            case 'ship to country code':
                $this->__set($key, self::COLUMN_COUNTRY);
                break;
            case 'postal_code':
            case 'postal code':
            case 'ship to zip':
                $this->__set($key, self::COLUMN_POSTAL_CODE);
                break;
            case 'phone':
            case 'buyer phone':
                $this->__set($key, self::COLUMN_PHONE);
                break;
            case 'city':
            case 'ship to city':
                $this->__set($key, self::COLUMN_CITY);
                break;
            case 'address':
            case 'ship to street':
                $this->__set($key, self::COLUMN_ADDRESS);
                break;
            case 'salutation_name':
                $this->__set($key, self::COLUMN_SALUTATION_NAME);
                break;
            case 'company':
            case 'company_name':
                $this->__set($key, self::COLUMN_COMPANY_NAME);
                break;
            case 'notes':
                $this->__set($key, self::COLUMN_NOTES);
                break;
            case 'sn_twitter':
                $this->__set($key, self::COLUMN_TWITTER);
                break;
            case 'sn_facebook':
                $this->__set($key, self::COLUMN_FACEBOOK);
                break;
            case 'sn_instagram':
                $this->__set($key, self::COLUMN_INSTAGRAM);
                break;
            case 'sn_soundcloud':
                $this->__set($key, self::COLUMN_SOUNDCLOUD);
                break;
            default:
                break;
            }
        }
    }
}
