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

namespace AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\Doctrine\DBAL\Types;

use ApiPlatform\Core\Api\IriConverterInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;

/**
 * JsonbIriAssociations.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class JsonbIriAssociations extends JsonArrayType
{
    /** @var IriConverterInterface */
    private $iriConverter;

    public function setIriConverter(IriConverterInterface $iriConverter): void
    {
        $this->iriConverter = $iriConverter;
    }

    private function getIriConverter(): IriConverterInterface
    {
        if (null === $this->iriConverter) {
            throw new \RuntimeException(sprintf('An instance of "%s" must be available. Call the "setIriConverter" method.', IriConverterInterface::class));
        }

        return $this->iriConverter;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new \RuntimeException(sprintf('Expected $value should be an array. Got %s.', gettype($value)));
        }

        $db = [];
        foreach ($value as $key => $object) {
            if (!is_object($object) || !$object instanceof IdentifiableInterface) {
                throw new \RuntimeException(sprintf('Expected value in array to be instance of %s. Got %s.', IdentifiableInterface::class, is_object($object) ? get_class($object) : gettype($object)));
            }

            $db[$key] = $this->getIriConverter()->getIriFromItem($object);
        }

        return json_encode($db);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || '' === $value) {
            return [];
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        $phpValue = [];
        $array = json_decode($value, true);
        foreach ($array as $key => $iri) {
            $phpValue[$key] = $this->getIriConverter()->getItemFromIri($iri);
        }

        return $phpValue;
    }

    public function getName()
    {
        return 'jsonb_iri_associations';
    }
}
