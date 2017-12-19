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

namespace AudienceHero\Bundle\ContactBundle\Repository\Specification;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * ContactAndContactsGroup.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactAndContactsGroup extends BaseSpecification
{
    /**
     * @var Contact
     */
    private $contact;
    /**
     * @var ContactsGroup
     */
    private $contactsGroup;

    public function __construct(Contact $contact, ContactsGroup $contactsGroup, ?string $dqlAlias = null)
    {
        $this->contact = $contact;
        $this->contactsGroup = $contactsGroup;

        parent::__construct($dqlAlias);
    }

    public function getSpec()
    {
        return Spec::andX(
            Spec::eq('contact', $this->contact),
            Spec::eq('group', $this->contactsGroup)
        );
    }
}
