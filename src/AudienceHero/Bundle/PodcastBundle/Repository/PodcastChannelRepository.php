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

namespace AudienceHero\Bundle\PodcastBundle\Repository;

use AudienceHero\Bundle\ActivityBundle\Repository\EntityCollectionBuilderRepositoryTrait;
use AudienceHero\Bundle\CoreBundle\Repository\PublishableEntityRepositoryTrait;
use AudienceHero\Bundle\CoreBundle\Repository\SearchableRepositoryTrait;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

class PodcastChannelRepository extends EntitySpecificationRepository
{
    use PublishableEntityRepositoryTrait;
    use SearchableRepositoryTrait;
    use EntityCollectionBuilderRepositoryTrait;
}
