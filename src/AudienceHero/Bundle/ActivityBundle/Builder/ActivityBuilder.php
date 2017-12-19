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

namespace AudienceHero\Bundle\ActivityBundle\Builder;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ActivityFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ActivityBuilder
{
    /** @var RequestStack */
    private $requestStack;
    /** @var ActivityRepository */
    private $repository;

    public function __construct(RequestStack $requestStack, ActivityRepository $repository)
    {
        $this->requestStack = $requestStack;
        $this->repository = $repository;
    }

    public function build(\DateTime $date, Person $owner, string $type, IdentifiableInterface $subject): Activity
    {
        $activity = new Activity();
        $activity->setOwner($owner);
        $activity->setType($type);
        $activity->addSubject($subject);
        $activity->setCreatedAt($date);
        $this->enrichFromRequest($activity);
        $this->repository->persist($activity);

        return $activity;
    }

    private function enrichFromRequest(Activity $activity): void
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        $activity->setRequest($request->server->getHeaders());
        $activity->setIp($request->getClientIp());
        $activity->setUserAgent($request->server->get('HTTP_USER_AGENT'));
        if ($referer = $request->server->get('HTTP_REFERER')) {
            $activity->setReferer($referer);
        }
    }
}
