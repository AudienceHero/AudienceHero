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

namespace AudienceHero\Bundle\PromoBundle\Mailer;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailFactory;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;
use AudienceHero\Bundle\PromoBundle\Factory\PromoCampaignEmailFactory;
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
     * @var PromoCampaignEmailFactory
     */
    private $factory;
    /**
     * @var EmailFactory
     */
    private $emailFactory;

    public function __construct(RendererAdapterInterface $rendererAdapter, SenderAdapterInterface $senderAdapter, PromoCampaignEmailFactory $factory, EmailFactory $emailFactory)
    {
        $this->rendererAdapter = $rendererAdapter;
        $this->senderAdapter = $senderAdapter;
        $this->factory = $factory;
        $this->emailFactory = $emailFactory;
    }

    public function send(PromoRecipient $promoRecipient, bool $isBoost)
    {
        $promo = $promoRecipient->getPromo();
        $email = null;
        if (!$isBoost) {
            $email = $this->factory->createClassic($promo, $promoRecipient);
        } else {
            $email = $this->factory->createClassicBoost($promo, $promoRecipient);
        }

        $data = ['promo_recipient' => $promoRecipient, 'promo' => $promo, 'mailing' => $promo->getMailing()];
        $data['mailing_recipient'] = !$isBoost ? $promoRecipient->getMailingRecipient() : $promoRecipient->getBoostMailingRecipient();
        $renderedEmail = $this->rendererAdapter->render($email, $data);

        if (!$isBoost) {
            $recipient = [$promoRecipient->getMailingRecipient()->getToEmail() => $promoRecipient->getMailingRecipient()->getToName()];
        } else {
            $recipient = [$promoRecipient->getBoostMailingRecipient()->getToEmail() => $promoRecipient->getBoostMailingRecipient()->getToName()];
        }

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

    public function sendPreview(Promo $promo, string $to)
    {
        $pr = new PromoRecipient();
        $pr->setOwner($promo->getOwner());
        $pr->setPromo($promo);
        $pr->setTest(true);

        $mr = new MailingRecipient();
        $cgc = new ContactsGroupContact();
        $c = new Contact();
        $cgc->setContact($c);
        $mr->setContactsGroupContact($cgc);
        $c->setSalutationName('AudienceHero Test Recipient');

        $mr->setOwner($promo->getOwner());
        $mr->setMailing($promo->getMailing());
        $mr->setToEmail($to);
        $mr->setToName('AudienceHero Test Recipient');
        $mr->setIsTest(true);

        $pr->setMailingRecipient($mr);

        $this->send($pr);
    }
}
