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

namespace AudienceHero\Bundle\ActivityBundle\CollectionBuilder;

/**
 * EntityCollectionBuilder.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ChainEntityCollectionBuilder
{
    private $builders = [];

    public function addCollectionBuilder(EntityCollectionBuilderInterface $collectionBuilder)
    {
        $this->builders[] = $collectionBuilder;
    }

    public function build(): array
    {
        $entities = [];
        foreach ($this->builders as $builder) {
            $built = $builder->build();
            foreach ($built as $id => $types) {
                if (isset($entities[$id])) {
                    $entities[$id] = array_merge($entities[$id], $types);
                } else {
                    $entities[$id] = $types;
                }
            }
        }

        return $entities;
    }
}
