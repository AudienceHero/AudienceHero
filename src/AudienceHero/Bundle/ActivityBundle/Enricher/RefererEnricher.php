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

namespace AudienceHero\Bundle\ActivityBundle\Enricher;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use Snowplow\RefererParser\Parser;

class RefererEnricher implements EnricherInterface
{
    private $parser;
    private $blacklist;

    /**
     * RefererEnricher constructor.
     *
     * @param Parser $parser
     * @param array  $blacklist
     *
     * TODO: Fill blacklist from dependency injection
     */
    public function __construct(Parser $parser, array $blacklist = [])
    {
        $this->parser = $parser;
        $this->blacklist = $blacklist;
    }

    public function enrich(Activity $activity): void
    {
        $referer = $activity->getReferer();
        if (empty($referer)) {
            return;
        }

        foreach ($this->blacklist as $blacklist) {
            if (preg_match(sprintf('|%s|', $blacklist), $referer)) {
                $activity->setIsSpam(true);

                return;
            }
        }

        $referer = $this->parser->parse($referer);
        if ($referer->isKnown()) {
            $activity->setRefererMedium($referer->getMedium());
            $activity->setRefererSource($referer->getSource());
            $activity->setRefererSearchTerm($referer->getSearchTerm() ?: null);
        }
    }
}
