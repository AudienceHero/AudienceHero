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

namespace AudienceHero\Bundle\ContactBundle\Linkable;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * ContactsGroupFormPopulator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactsGroupFormPopulator implements LinkablePopulatorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function supports(LinkableInterface $object): bool
    {
        return $object instanceof ContactsGroupForm;
    }

    public function populate(LinkableInterface $object)
    {
        $object->setURL('public', $this->generator->generate('contacts_group_forms_optin_request', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
        $object->setURL('print', $this->generator->generate('contacts_group_forms_print', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
