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

namespace AudienceHero\Bundle\ActivityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * DeviceTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait DeviceTrait
{
    /**
     * @var null|string
     *
     * @ORM\Column(name="user_agent", type="text", nullable=true)
     * @Groups({"read"})
     */
    private $userAgent;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_bot", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isBot;

    /**
     * @var null|string
     *
     * @ORM\Column(name="bot", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $bot;

    /**
     * @var null|string
     *
     * @ORM\Column(name="bot_category", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $botCategory;

    /**
     * @var null|string
     *
     * @ORM\Column(name="os", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $os;

    /**
     * @var null|string
     *
     * @ORM\Column(name="os_version", type="string", length=16, nullable=true)
     * @Groups({"read"})
     */
    private $osVersion;

    /**
     * @var null|string
     *
     * @ORM\Column(name="client", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $client;

    /**
     * @var null|string
     *
     * @ORM\Column(name="client_engine", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $clientEngine;

    /**
     * @var null|string
     *
     * @ORM\Column(name="client_version", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $clientVersion;

    /**
     * @var null|string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $brand;

    /**
     * @var null|string
     *
     * @ORM\Column(name="model", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $model;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_touch_enabled", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isTouchEnabled;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_desktop", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isDesktop;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_mobile", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isMobile;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_feature_phone", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isFeaturePhone;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_console", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isConsole;

    /**
     * @var null|string
     *
     * @ORM\Column(name="device", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $device;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_smartphone", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isSmartphone;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_tablet", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isTablet;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_phablet", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isPhablet;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_portable_media_player", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isPortableMediaPlayer;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_car_browser", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isCarBrowser;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_tv", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isTV;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_smart_display", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isSmartDisplay;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_camera", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isCamera;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_browser", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isBrowser;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_feed_reader", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isFeedReader;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_mobile_app", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isMobileApp;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_pim", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isPIM;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_library", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isLibrary;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="is_media_player", type="boolean", nullable=true)
     * @Groups({"read"})
     */
    private $isMediaPlayer;

    public function setIsFeaturePhone(?bool $isFeaturePhone): void
    {
        $this->isFeaturePhone = $isFeaturePhone;
    }

    public function getIsFeaturePhone(): ?bool
    {
        return $this->isFeaturePhone;
    }

    public function setIsTablet(?bool $isTablet): void
    {
        $this->isTablet = $isTablet;
    }

    public function getIsTablet(): ?bool
    {
        return $this->isTablet;
    }

    public function setIsConsole(?bool $isConsole): void
    {
        $this->isConsole = $isConsole;
    }

    public function getIsConsole(): ?bool
    {
        return $this->isConsole;
    }

    public function setIsTV(?bool $isTV): void
    {
        $this->isTV = $isTV;
    }

    public function getIsTV(): ?bool
    {
        return $this->isTV;
    }

    public function setIsSmartDisplay(?bool $isSmartDisplay): void
    {
        $this->isSmartDisplay = $isSmartDisplay;
    }

    public function getIsSmartDisplay(): ?bool
    {
        return $this->isSmartDisplay;
    }

    public function setIsCamera(?bool $isCamera): void
    {
        $this->isCamera = $isCamera;
    }

    public function getIsCamera(): ?bool
    {
        return $this->isCamera;
    }

    public function setIsBrowser(?bool $isBrowser): void
    {
        $this->isBrowser = $isBrowser;
    }

    public function getIsBrowser(): ?bool
    {
        return $this->isBrowser;
    }

    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setIsBot(?bool $isBot): void
    {
        $this->isBot = $isBot;
    }

    public function getIsBot(): ?bool
    {
        return $this->isBot;
    }

    public function setBot(?string $bot): void
    {
        $this->bot = $bot;
    }

    public function getBot(): ?string
    {
        return $this->bot;
    }

    public function setBotCategory(?string $botCategory): void
    {
        $this->botCategory = $botCategory;
    }

    public function getBotCategory(): ?string
    {
        return $this->botCategory;
    }

    public function setOs(string $os): void
    {
        $this->os = $os;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOsVersion(?string $osVersion): void
    {
        $this->osVersion = $osVersion;
    }

    public function getOsVersion(): ?string
    {
        return $this->osVersion;
    }

    public function setClient(?string $client): void
    {
        $this->client = $client;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClientVersion(?string $clientVersion): void
    {
        $this->clientVersion = $clientVersion;
    }

    public function getClientVersion(): ?string
    {
        return $this->clientVersion;
    }

    public function setClientEngine(?string $clientEngine): void
    {
        $this->clientEngine = $clientEngine;
    }

    public function getClientEngine(): ?string
    {
        return $this->clientEngine;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setModel(?string $model): void
    {
        $this->model = $model;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setIsTouchEnabled(?bool $isTouchEnabled): void
    {
        $this->isTouchEnabled = $isTouchEnabled;
    }

    public function getIsTouchEnabled(): ?bool
    {
        return $this->isTouchEnabled;
    }

    public function setIsDesktop(?bool $isDesktop): void
    {
        $this->isDesktop = $isDesktop;
    }

    public function getIsDesktop(): ?bool
    {
        return $this->isDesktop;
    }

    public function setIsMobile(?bool $isMobile): void
    {
        $this->isMobile = $isMobile;
    }

    public function getIsMobile(): ?bool
    {
        return $this->isMobile;
    }

    public function setDevice(?string $device): void
    {
        $this->device = $device;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setIsSmartphone(?bool $isSmartphone): void
    {
        $this->isSmartphone = $isSmartphone;
    }

    public function getIsSmartphone(): ?bool
    {
        return $this->isSmartphone;
    }

    public function setIsPhablet(?bool $isPhablet): void
    {
        $this->isPhablet = $isPhablet;
    }

    public function getIsPhablet(): ?bool
    {
        return $this->isPhablet;
    }

    public function setIsPortableMediaPlayer(?bool $isPortableMediaPlayer): void
    {
        $this->isPortableMediaPlayer = $isPortableMediaPlayer;
    }

    public function getIsPortableMediaPlayer(): ?bool
    {
        return $this->isPortableMediaPlayer;
    }

    public function setIsCarBrowser(?bool $isCarBrowser): void
    {
        $this->isCarBrowser = $isCarBrowser;
    }

    public function getIsCarBrowser(): ?bool
    {
        return $this->isCarBrowser;
    }

    public function setIsFeedReader(?bool $isFeedReader): void
    {
        $this->isFeedReader = $isFeedReader;
    }

    public function getIsFeedReader(): ?bool
    {
        return $this->isFeedReader;
    }

    public function setIsMobileApp(?bool $isMobileApp): void
    {
        $this->isMobileApp = $isMobileApp;
    }

    public function getIsMobileApp(): ?bool
    {
        return $this->isMobileApp;
    }

    public function setIsPIM(?bool $isPIM): void
    {
        $this->isPIM = $isPIM;
    }

    public function getIsPIM(): ?bool
    {
        return $this->isPIM;
    }

    public function setIsLibrary(?bool $isLibrary): void
    {
        $this->isLibrary = $isLibrary;
    }

    public function getIsLibrary(): ?bool
    {
        return $this->isLibrary;
    }

    public function setIsMediaPlayer(?bool $isMediaPlayer): void
    {
        $this->isMediaPlayer = $isMediaPlayer;
    }

    public function getIsMediaPlayer(): ?bool
    {
        return $this->isMediaPlayer;
    }
}
