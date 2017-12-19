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

namespace AudienceHero\Bundle\ActivityBundle\Tests\Enricher;

use AudienceHero\Bundle\ActivityBundle\Enricher\DeviceEnricher;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use DeviceDetector\Yaml\Symfony;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;

class DeviceEnricherTest extends TestCase
{
    private static $cache;
    private $enricher;

    public static function setUpBeforeClass()
    {
        static::$cache = new ArrayCache();
    }

    public static function tearDownAfterClass()
    {
        static::$cache = null;
    }

    public function setUp()
    {
        $reader = new Symfony();
        $this->enricher = new DeviceEnricher(static::$cache, $reader);
    }

    public function testEnricherWithNullUserAgent()
    {
        $activity = new Activity();
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertNull($activity->getClient());
        $this->assertNull($activity->getClientVersion());
        $this->assertNull($activity->getClientEngine());
        $this->assertNull($activity->getOs());
        $this->assertNull($activity->getBrand());
        $this->assertNull($activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertFalse($activity->getIsDesktop());
        $this->assertFalse($activity->getIsMobile());
        $this->assertNull($activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertFalse($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithBrowserUserAgent()
    {
        $activity = new Activity();
        $activity->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36');
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('Chrome', $activity->getClient());
        $this->assertEquals('51.0', $activity->getClientVersion());
        $this->assertEquals('Blink', $activity->getClientEngine());
        $this->assertEquals('Mac', $activity->getOs());
        $this->assertEquals('10.11', $activity->getOsVersion());
        $this->assertNull($activity->getBrand());
        $this->assertNull($activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertTrue($activity->getIsDesktop());
        $this->assertFalse($activity->getIsMobile());
        $this->assertEquals('desktop', $activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertTrue($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithMobileUserAgent()
    {
        $activity = new Activity();
        $ua = 'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30';
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('Android Browser', $activity->getClient());
        $this->assertEquals('', $activity->getClientVersion());
        $this->assertEquals('WebKit', $activity->getClientEngine());
        $this->assertEquals('Android', $activity->getOs());
        $this->assertEquals('4.0', $activity->getOsVersion());
        $this->assertEquals('LG', $activity->getBrand());
        $this->assertEquals('L160L', $activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertFalse($activity->getIsDesktop());
        $this->assertTrue($activity->getIsMobile());
        $this->assertEquals('smartphone', $activity->getDevice());
        $this->assertTrue($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertTrue($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithLibrary()
    {
        $ua = 'curl/7.7.x (i386--freebsd4.3) libcurl 7.7.x (SSL 0.9.6) (ipv6 enabled)';
        $activity = new Activity();
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('curl', $activity->getClient());
        $this->assertEquals('7.7', $activity->getClientVersion());
        $this->assertEquals('', $activity->getClientEngine());
        $this->assertNull($activity->getOs());
        $this->assertNull($activity->getOsVersion());
        $this->assertNull($activity->getBrand());
        $this->assertNull($activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertFalse($activity->getIsDesktop());
        $this->assertFalse($activity->getIsMobile());
        $this->assertNull($activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertFalse($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertTrue($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithFeedReader()
    {
        $ua = 'Reeder/1020.09.00 CFNetwork/596.2.3 Darwin/12.2.0 (x86_64) (MacBookPro8%2C2)';
        $activity = new Activity();
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('Reeder', $activity->getClient());
        $this->assertEquals('1020.09', $activity->getClientVersion());
        $this->assertEquals('', $activity->getClientEngine());
        $this->assertEquals('Mac', $activity->getOs());
        $this->assertEquals('10.8', $activity->getOsVersion());
        $this->assertEquals('Apple', $activity->getBrand());
        $this->assertNull($activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertTrue($activity->getIsDesktop());
        $this->assertFalse($activity->getIsMobile());
        $this->assertEquals('desktop', $activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertFalse($activity->getIsBrowser());
        $this->assertTrue($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithConsole()
    {
        $ua = 'Mozilla/5.0 (PLAYSTATION 3; 2.00)';
        $activity = new Activity();
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('NetFront', $activity->getClient());
        $this->assertEquals('', $activity->getClientVersion());
        $this->assertEquals('', $activity->getClientEngine());
        $this->assertEquals('PlayStation', $activity->getOs());
        $this->assertEquals('3.0', $activity->getOsVersion());
        $this->assertEquals('Sony', $activity->getBrand());
        $this->assertEquals('PlayStation 3', $activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertFalse($activity->getIsDesktop());
        $this->assertFalse($activity->getIsMobile());
        $this->assertEquals('console', $activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertTrue($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertTrue($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithTablet()
    {
        $ua = 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25';
        $activity = new Activity();
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('Mobile Safari', $activity->getClient());
        $this->assertEquals('6.0', $activity->getClientVersion());
        $this->assertEquals('WebKit', $activity->getClientEngine());
        //$this->assertNull($activity->getOs());
        $this->assertEquals('Apple', $activity->getBrand());
        $this->assertEquals('iPad', $activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertFalse($activity->getIsDesktop());
        $this->assertTrue($activity->getIsMobile());
        $this->assertEquals('tablet', $activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertTrue($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertTrue($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertFalse($activity->getIsMediaPlayer());
    }

    public function testEnricherWithMediaPlayer()
    {
        $ua = 'iTunes/9.0.3';
        $activity = new Activity();
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertFalse($activity->getIsBot());
        $this->assertNull($activity->getBot());
        $this->assertNull($activity->getBotCategory());
        $this->assertEquals('iTunes', $activity->getClient());
        $this->assertEquals('9.0', $activity->getClientVersion());
        $this->assertNull($activity->getClientEngine());
        $this->assertNull($activity->getOs());
        $this->assertNull($activity->getOsVersion());
        $this->assertNull($activity->getBrand());
        $this->assertNull($activity->getModel());
        $this->assertFalse($activity->getIsTouchEnabled());
        $this->assertFalse($activity->getIsDesktop());
        $this->assertFalse($activity->getIsMobile());
        $this->assertNull($activity->getDevice());
        $this->assertFalse($activity->getIsSmartphone());
        $this->assertFalse($activity->getIsFeaturePhone());
        $this->assertFalse($activity->getIsTablet());
        $this->assertFalse($activity->getIsPhablet());
        $this->assertFalse($activity->getIsConsole());
        $this->assertFalse($activity->getIsPortableMediaPlayer());
        $this->assertFalse($activity->getIsCarBrowser());
        $this->assertFalse($activity->getIsTV());
        $this->assertFalse($activity->getIsSmartDisplay());
        $this->assertFalse($activity->getIsCamera());
        $this->assertFalse($activity->getIsBrowser());
        $this->assertFalse($activity->getIsFeedReader());
        $this->assertFalse($activity->getIsMobileApp());
        $this->assertFalse($activity->getIsPIM());
        $this->assertFalse($activity->getIsLibrary());
        $this->assertTrue($activity->getIsMediaPlayer());
    }

    public function testEnricherWithBot()
    {
        $ua = 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $activity = new Activity();
        $activity->setUserAgent($ua);
        $this->enricher->enrich($activity);

        $this->assertTrue($activity->getIsBot());
        $this->assertSame('Googlebot', $activity->getBot());
        $this->assertSame('Search bot', $activity->getBotCategory());
        $this->assertNull($activity->getClient());
        $this->assertNull($activity->getClientVersion());
        $this->assertNull($activity->getClientEngine());
        $this->assertNull($activity->getOs());
        $this->assertNull($activity->getOsVersion());
        $this->assertNull($activity->getBrand());
        $this->assertNull($activity->getModel());
        $this->assertNull($activity->getIsTouchEnabled());
        $this->assertNull($activity->getIsDesktop());
        $this->assertNull($activity->getIsMobile());
        $this->assertNull($activity->getDevice());
        $this->assertNull($activity->getIsSmartphone());
        $this->assertNull($activity->getIsFeaturePhone());
        $this->assertNull($activity->getIsTablet());
        $this->assertNull($activity->getIsPhablet());
        $this->assertNull($activity->getIsConsole());
        $this->assertNull($activity->getIsPortableMediaPlayer());
        $this->assertNull($activity->getIsCarBrowser());
        $this->assertNull($activity->getIsTV());
        $this->assertNull($activity->getIsSmartDisplay());
        $this->assertNull($activity->getIsCamera());
        $this->assertNull($activity->getIsBrowser());
        $this->assertNull($activity->getIsFeedReader());
        $this->assertNull($activity->getIsMobileApp());
        $this->assertNull($activity->getIsPIM());
        $this->assertNull($activity->getIsLibrary());
        $this->assertNull($activity->getIsMediaPlayer());
    }
}
