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

namespace AudienceHero\Bundle\ContactBundle\Manager;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Repository\ContactRepository;
use AudienceHero\Bundle\ContactBundle\Repository\ContactsGroupContactRepository;
use AudienceHero\Bundle\ContactBundle\Repository\Specification\ContactAndContactsGroup;
use AudienceHero\Bundle\ContactBundle\Repository\Specification\EmailOrPhone;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Repository\Specification\OwnedBy;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Webmozart\Assert\Assert;

/**
 * ContactManager.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactManager
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var ContactsGroupContactRepository
     */
    private $cgcRepository;

    public function __construct(RegistryInterface $registry, ContactRepository $contactRepository, ContactsGroupContactRepository $cgcRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->registry = $registry;
        $this->cgcRepository = $cgcRepository;
    }

    public function add(Contact $contact): Contact
    {
        Assert::notNull($contact->getOwner(), 'Contact must have an owner');
        if (!$contact->getEmail() && !$contact->getPhone()) {
            throw new \InvalidArgumentException('Contact must have either an email or a phone number attached');
        }

        /** @var null|Contact $existing */
        $existing = $this->contactRepository->matchOneOrNullResult(
            Spec::andX(
                new OwnedBy($contact->getOwner()),
                new EmailOrPhone($contact->getEmail(), $contact->getPhone())
            )
        );
        if ($existing) {
            $existing->merge($contact);
            $contact = $existing;
        } else {
            $em = $this->registry->getManager();
            $em->persist($contact);
        }

        return $contact;
    }

    public function addToGroup(Contact $contact, ContactsGroup $contactsGroup): ContactsGroupContact
    {
        if ($contact->getId() && $contactsGroup->getId()) {
            $result = $this->cgcRepository->matchOneOrNullResult(new ContactAndContactsGroup($contact, $contactsGroup));
            if ($result) {
                return $result;
            }
        }

        $cgc = new ContactsGroupContact();
        $cgc->setContact($contact);
        $cgc->setGroup($contactsGroup);
        $em = $this->registry->getManager();
        $em->persist($cgc);

        return $cgc;
    }
}
