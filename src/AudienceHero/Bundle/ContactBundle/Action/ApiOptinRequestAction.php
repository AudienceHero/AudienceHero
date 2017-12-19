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

namespace AudienceHero\Bundle\ContactBundle\Action;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Factory\ContactFactory;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\CoreBundle\Validator\Assert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ApiOptinRequestAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ApiOptinRequestAction
{
    /**
     * @var ContactManager
     */
    private $contactManager;
    /**
     * @var OptManager
     */
    private $optManager;
    /**
     * @var ContactFactory
     */
    private $contactFactory;

    public function __construct(ContactFactory $contactFactory, ContactManager $contactManager, OptManager $optManager)
    {
        $this->contactFactory = $contactFactory;
        $this->contactManager = $contactManager;
        $this->optManager = $optManager;
    }

    /**
     * @Route("/api/contacts_group_forms/{id}/optin",
     *        name="api_contacts_group_forms_optin",
     *        defaults={"_api_resource_class"=ContactsGroupForm::class, "_api_item_operation_name"="optin"}
     *       )
     * @Method("POST")
     */
    public function __invoke(Request $request, ContactsGroupForm $data)
    {
        Assert::validJSONRequest($request);

        $contact = $this->contactFactory->createFromJson($request->getContent(), $data->getOwner());
        $contact = $this->contactManager->add($contact);
        $cgc = $this->contactManager->addToGroup($contact, $data->getContactsGroup());
        $this->optManager->dispatchOptInRequest($cgc, $data);

        return $data;
    }
}
