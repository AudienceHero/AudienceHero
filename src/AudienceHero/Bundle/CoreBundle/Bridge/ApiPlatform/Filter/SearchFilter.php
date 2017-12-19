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

namespace AudienceHero\Bundle\CoreBundle\Bridge\ApiPlatform\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * SearchFilter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SearchFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        list($query) = $this->extractProperties($request, $resourceClass);
        if (!$query) {
            return;
        }

        $this->filterProperty('q', $query, $queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
    }

    /**
     * Passes a property through the filter.
     *
     * @param string                      $property
     * @param mixed                       $value
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param string|null                 $operationName
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $parameterName = $queryNameGenerator->generateParameterName($property); // Generate a unique parameter name to avoid collisions with other filters
        $queryBuilder
            ->andWhere(sprintf('PLAINTO_TSQUERY(o.tsv, :%s) = true', $parameterName))
            ->setParameter($parameterName, $value)
        ;
    }

    /**
     * Gets the description of this filter for the given resource.
     *
     * Returns an array with the filter parameter names as keys and array with the following data as values:
     *   - property: the property where the filter is applied
     *   - type: the type of the filter
     *   - required: if this filter is required
     *   - strategy: the used strategy
     *   - swagger (optional): additional parameters for the path operation, e.g. 'swagger' => ['description' => 'My Description']
     * The description can contain additional data specific to a filter.
     *
     * @param string $resourceClass
     *
     * @return array
     */
    public function getDescription(string $resourceClass): array
    {
        $description = [];
        $description['q'] = [
            'property' => 'q',
            'type' => 'string',
            'required' => false,
            'swagger' => ['description' => 'Perform a full-text search.'],
        ];

        return $description;
    }

    protected function extractProperties(Request $request): array
    {
        return [$request->query->get('q', null)];
    }
}
