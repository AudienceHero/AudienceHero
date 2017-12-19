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

namespace AudienceHero\Bundle\ContactBundle\Util;

/**
 * TextCleaner.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SocialHandleCleaner
{
    const REGEX_TWITTER = "/((@)|(.*\.?twitter.com\/))(?P<handle>[a-z-0-9_]{1,15})/i";
    const REGEX_MIXCLOUD = "/((@)|(.*\.?mixcloud.com\/))(?P<handle>[a-z-0-9_-]{1,30})/i";
    const REGEX_SOUNDCLOUD = "/((@)|(.*\.?soundcloud.com\/))(?P<handle>[a-z-0-9_-]{3,255})/i";
    const REGEX_FACEBOOK = "/((@)|(.*\.?facebook.com\/))(?P<handle>[a-z-0-9_.-]{3,255})/i";
    const REGEX_INSTAGRAM = "/((@)|(.*\.?instagram.com\/))(?P<handle>[a-z-0-9_-]{3,255})/i";

    public static function extract($regex, $haystack)
    {
        if (0 === strlen($haystack)) {
            return;
        }

        if (false === strpos($haystack, '@') && false === strpos($haystack, '/')) {
            return $haystack;
        }

        $results = [];
        preg_match($regex, $haystack, $results);
        if (!isset($results['handle'])) {
            return;
        }

        return $results['handle'];
    }

    public static function twitter($haystack)
    {
        return static::extract(self::REGEX_TWITTER, $haystack);
    }

    public static function mixcloud($haystack)
    {
        return static::extract(self::REGEX_MIXCLOUD, $haystack);
    }

    public static function soundcloud($haystack)
    {
        return static::extract(self::REGEX_SOUNDCLOUD, $haystack);
    }

    public static function facebook($haystack)
    {
        return static::extract(self::REGEX_FACEBOOK, $haystack);
    }

    public static function instagram($haystack)
    {
        return static::extract(self::REGEX_INSTAGRAM, $haystack);
    }
}
