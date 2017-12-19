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

namespace AudienceHero\Bundle\ActivityBundle\Repository;

use ActivityBundle\Entity\Activity;
use AppBundle\Entity\PodcastChannel;
use AppBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use Doctrine\ORM\NoResultException;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

class AggregateRepository extends EntitySpecificationRepository
{
    public function findOrCreateOneBySubjectIdAndType(string $id, string $type)
    {
        $aggregate = $this->findOneBy(['subjectId' => $id, 'type' => $type]);
        if (!$aggregate) {
            $aggregate = new Aggregate();
            $aggregate->setSubjectId($id);
            $aggregate->setType($type);
        }

        return $aggregate;
    }
}
