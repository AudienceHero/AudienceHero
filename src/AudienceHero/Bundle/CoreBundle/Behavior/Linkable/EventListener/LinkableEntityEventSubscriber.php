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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Linkable\EventListener;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorChain;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * LocatableEntityEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class LinkableEntityEventSubscriber implements EventSubscriber
{
    /**
     * @var LinkablePopulatorChain
     */
    private $populator;

    public function __construct(LinkablePopulatorChain $populator)
    {
        $this->populator = $populator;
    }

    public function getSubscribedEvents()
    {
        return [
            'postLoad',
        ];
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof LinkableInterface) {
            return;
        }

        $this->populator->populate($entity);
    }
}
