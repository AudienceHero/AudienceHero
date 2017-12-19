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

namespace AudienceHero\Bundle\ContactBundle\Tests\Util;

use AudienceHero\Bundle\ContactBundle\Util\SocialHandleCleaner;
use PHPUnit\Framework\TestCase;

class SocialHandleCleanerTest extends TestCase
{
    /**
     * @dataProvider provideTwitter
     */
    public function testTwitter($haystack, $expected)
    {
        $this->assertEquals($expected, \AudienceHero\Bundle\ContactBundle\Util\SocialHandleCleaner::twitter($haystack));
    }

    /**
     * @dataProvider provideFacebook
     */
    public function testFacebook($haystack, $expected)
    {
        $this->assertEquals($expected, \AudienceHero\Bundle\ContactBundle\Util\SocialHandleCleaner::facebook($haystack));
    }

    /**
     * @dataProvider provideInstagram
     */
    public function testInstagram($haystack, $expected)
    {
        $this->assertEquals($expected, \AudienceHero\Bundle\ContactBundle\Util\SocialHandleCleaner::instagram($haystack));
    }

    /**
     * @dataProvider provideSoundcloud
     */
    public function testSoundcloud($haystack, $expected)
    {
        $this->assertEquals($expected, \AudienceHero\Bundle\ContactBundle\Util\SocialHandleCleaner::soundcloud($haystack));
    }

    /**
     * @dataProvider provideMixcloud
     */
    public function testMixcloud($haystack, $expected)
    {
        $this->assertEquals($expected, SocialHandleCleaner::mixcloud($haystack));
    }

    public function provideTwitter()
    {
        return [
            ['fuTuReCat_', 'fuTuReCat_'],
            ['@fuTuReCat_', 'fuTuReCat_'],
            ['www.twitter.com/futurecat', 'futurecat'],
            ['wob.twitter.com/futurecat', 'futurecat'],
            ['m.twitter.com/futurecat', 'futurecat'],
            ['http://www.twitter.com/futurecat', 'futurecat'],
            ['http://wob.twitter.com/futurecat?utm_medium=wob', 'futurecat'],
            ['https://twitter.com/futurecat.medium', 'futurecat'],
            ['', null],
            ['https://facebook.com/futurecat', null],
        ];
    }

    public function provideInstagram()
    {
        return [
            ['fuTuReCat_', 'fuTuReCat_'],
            ['@fuTuReCat_', 'fuTuReCat_'],
            ['www.instagram.com/futurecat', 'futurecat'],
            ['wob.instagram.com/futurecat', 'futurecat'],
            ['m.instagram.com/futurecat', 'futurecat'],
            ['http://www.instagram.com/futurecat', 'futurecat'],
            ['http://wob.instagram.com/futurecat?utm_medium=wob', 'futurecat'],
            ['https://instagram.com/futurecat.medium', 'futurecat'],
            ['', null],
            ['https://facebook.com/futurecat',  null],
        ];
    }

    public function provideSoundcloud()
    {
        return [
            ['fuTuReCat_', 'fuTuReCat_'],
            ['@fuTuReCat_', 'fuTuReCat_'],
            ['www.soundcloud.com/futurecat', 'futurecat'],
            ['wob.soundcloud.com/futurecat', 'futurecat'],
            ['wob.soundcloud.com/futurecatas-asdfasdfasf-asdf_ASdf-asdf-asdf-asdf', 'futurecatas-asdfasdfasf-asdf_ASdf-asdf-asdf-asdf'],
            ['m.soundcloud.com/futurecat', 'futurecat'],
            ['http://www.soundcloud.com/futurecat', 'futurecat'],
            ['http://wob.soundcloud.com/futurecat?utm_medium=wob', 'futurecat'],
            ['https://soundcloud.com/futurecat.medium', 'futurecat'],
            ['', null],
            ['https://facebook.com/futurecat', null],
        ];
    }

    public function provideFacebook()
    {
        return [
            ['fuTuReCat_', 'fuTuReCat_'],
            ['@fuTuReCat_', 'fuTuReCat_'],
            ['@fuTuRe.Cat_', 'fuTuRe.Cat_'],
            ['www.facebook.com/futurecat', 'futurecat'],
            ['wob.facebook.com/futurecat', 'futurecat'],
            ['wob.facebook.com/futurecatas-asdfasdfasf-asdf_ASdf-asdf-asdf-asdf', 'futurecatas-asdfasdfasf-asdf_ASdf-asdf-asdf-asdf'],
            ['m.facebook.com/future.cat', 'future.cat'],
            ['http://www.facebook.com/futurecat', 'futurecat'],
            ['http://wob.facebook.com/futurecat?utm_medium=wob', 'futurecat'],
            ['https://facebook.com/futurecat.medium', 'futurecat.medium'],
            ['', null],
            ['https://soundcloud.com/futurecat', null],
        ];
    }

    public function provideMixcloud()
    {
        return [
            ['fuTuReCat_', 'fuTuReCat_'],
            ['@fuTuReCat_', 'fuTuReCat_'],
            ['www.mixcloud.com/futurecat', 'futurecat'],
            ['wob.mixcloud.com/futurecat09', 'futurecat09'],
            ['wob.mixcloud.com/futurecat', 'futurecat'],
            ['m.mixcloud.com/futurecat', 'futurecat'],
            ['http://www.mixcloud.com/futurecat', 'futurecat'],
            ['http://wob.mixcloud.com/futurecat?utm_medium=wob', 'futurecat'],
            ['https://mixcloud.com/futurecat.medium', 'futurecat'],
            ['', null],
            ['https://soundcloud.com/futurecat', null],
        ];
    }
}
