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

namespace AudienceHero\Bundle\MailingCampaignBundle\Mailer;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingCampaignEmailFactory;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;

/**
 * Mailer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Mailer implements MailerInterface
{
    /**
     * @var RendererAdapterInterface
     */
    private $rendererAdapter;
    /**
     * @var SenderAdapterInterface
     */
    private $senderAdapter;
    /**
     * @var MailingCampaignEmailFactory
     */
    private $factory;
    /**
     * @var EmailFactory
     */
    private $emailFactory;

    public function __construct(RendererAdapterInterface $rendererAdapter, SenderAdapterInterface $senderAdapter, MailingCampaignEmailFactory $factory, EmailFactory $emailFactory)
    {
        $this->rendererAdapter = $rendererAdapter;
        $this->senderAdapter = $senderAdapter;
        $this->factory = $factory;
        $this->emailFactory = $emailFactory;
    }

    public function send(MailingRecipient $mailingRecipient)
    {
        $mailing = $mailingRecipient->getMailing();
        $email = $this->factory->createClassic($mailing, $mailingRecipient);

        $data = ['mailing_recipient' => $mailingRecipient, 'mailing' => $mailing];
        $renderedEmail = $this->rendererAdapter->render($email, $data);
        $recipient = [$mailingRecipient->getToEmail() => $mailingRecipient->getToName()];

        $this->senderAdapter->send(
            $recipient,
            $email->getSenderAddress(),
            $email->getSenderName(),
            $renderedEmail,
            $email,
            $data,
            []
        );
    }

    public function sendPreview(Mailing $mailing, string $to)
    {
        $mr = new MailingRecipient();
        $cgc = new ContactsGroupContact();
        $cgc->setOwner($mailing->getOwner());

        $c = new Contact();
        $c->setName($to);
        $c->setEmail($to);
        $c->setOwner($mailing->getOwner());
        $cgc->setContact($c);
        $mr->setContactsGroupContact($cgc);
        $c->setSalutationName('AudienceHero Test Recipient');

        $mr->setMailing($mailing);
        $mr->setToEmail($to);
        $mr->setToName('AudienceHero Test Recipient');
        $mr->setIsTest(true);

        $this->send($mr);
    }
}
