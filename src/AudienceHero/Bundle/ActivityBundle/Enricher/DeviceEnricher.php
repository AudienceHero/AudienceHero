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

namespace AudienceHero\Bundle\ActivityBundle\Enricher;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Yaml\Parser;
use Doctrine\Common\Cache\Cache;

/**
 * DeviceEnricher.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class DeviceEnricher implements EnricherInterface
{
    /** @var Cache */
    private $cache;
    /** @var Parser */
    private $parser;

    public function __construct(Cache $cache, Parser $parser)
    {
        $this->cache = $cache;
        $this->parser = $parser;
    }

    public function enrich(Activity $activity): void
    {
        $dd = new DeviceDetector($activity->getUserAgent());
        $dd->setCache($this->cache);
        $dd->setYamlParser($this->parser);

        $dd->parse();

        $activity->setIsBot($dd->isBot());
        if ($dd->isBot()) {
            $bot = $dd->getBot();
            $activity->setBot($bot['name']);
            if (isset($bot['category'])) {
                $activity->setBotCategory($bot['category']);
            }

            return;
        }

        $client = $dd->getClient();
        $activity->setClient($client['name']);
        if (isset($client['version'])) {
            $activity->setClientVersion($client['version']);
        }

        if (isset($client['engine'])) {
            $activity->setClientEngine($client['engine']);
        }

        $os = $dd->getOs();
        if (is_array($os) && isset($os['name'])) {
            $activity->setOs($os['name']);
        }
        if (is_array($os) && isset($os['version'])) {
            $activity->setOsVersion($os['version']);
        }

        $activity->setBrand($this->nullEmpty($dd->getBrandName()));
        $activity->setModel($this->nullEmpty($dd->getModel()));
        $activity->setIsTouchEnabled($this->nullEmpty($dd->isTouchEnabled()));
        $activity->setIsDesktop($this->nullEmpty($dd->isDesktop()));
        $activity->setIsMobile($this->nullEmpty($dd->isMobile()));
        $activity->setDevice($this->nullEmpty($dd->getDeviceName()));
        $activity->setIsSmartphone($this->nullEmpty($dd->isSmartphone()));
        $activity->setIsFeaturePhone($this->nullEmpty($dd->isFeaturePhone()));
        $activity->setIsTablet($this->nullEmpty($dd->isTablet()));
        $activity->setIsPhablet($this->nullEmpty($dd->isPhablet()));
        $activity->setIsConsole($this->nullEmpty($dd->isConsole()));
        $activity->setIsPortableMediaPlayer($this->nullEmpty($dd->isPortableMediaPlayer()));
        $activity->setIsCarBrowser($this->nullEmpty($dd->isCarBrowser()));
        $activity->setIsTV($this->nullEmpty($dd->isTV()));
        $activity->setIsSmartDisplay($this->nullEmpty($dd->isSmartDisplay()));
        $activity->setIsCamera($this->nullEmpty($dd->isCamera()));
        $activity->setIsBrowser($this->nullEmpty($dd->isBrowser()));
        $activity->setIsFeedReader($this->nullEmpty($dd->isFeedReader()));
        $activity->setIsMobileApp($this->nullEmpty($dd->isMobileApp()));
        $activity->setIsPIM($this->nullEmpty($dd->isPIM()));
        $activity->setIsLibrary($this->nullEmpty($dd->isLibrary()));
        $activity->setIsMediaPlayer($this->nullEmpty($dd->isMediaPlayer()));
    }

    private function nullEmpty($value)
    {
        if (false === $value) {
            return false;
        }

        if (empty($value)) {
            return;
        }

        if (is_array($value)) {
            return true;
        }

        return $value;
    }
}
