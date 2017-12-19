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

namespace AudienceHero\Bundle\PromoBundle\Linkable;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * PromoLocatablePopulator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoLinkablePopulator implements LinkablePopulatorInterface
{
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function supports(LinkableInterface $object): bool
    {
        return $object instanceof Promo;
    }

    public function populate(LinkableInterface $object)
    {
        $object->setURL('preview', $this->router->generate('promos_preview', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
