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

namespace AudienceHero\Bundle\FileBundle\EventListener;

use AudienceHero\Bundle\CoreBundle\Event\CoreEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * PostFixturesLoadEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PostFixturesLoadEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $kernelRootDir;

    public function __construct(string $kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::POST_FIXTURES_LOAD => 'onPostFixturesLoad',
        ];
    }

    public function onPostFixturesLoad(Event $event)
    {
        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/../Resources/fixtures/assets', sprintf('%s/web/upload', $this->kernelRootDir));
    }
}
