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

namespace AudienceHero\Bundle\CoreBundle\Serializer\Normalizer;

use AudienceHero\Bundle\CoreBundle\Queue\Message;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/**
 * MessageNormalizer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MessageNormalizer extends AbstractObjectNormalizer
{
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(RegistryInterface $registry, PropertyAccessorInterface $propertyAccessor, ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null)
    {
        $this->registry = $registry;
        $this->propertyAccessor = $propertyAccessor;

        parent::__construct($classMetadataFactory, $nameConverter, $propertyTypeExtractor);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Message;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if (Message::class === $type) {
            return true;
        }

        $rc = new \ReflectionClass($type);

        return $rc->isSubclassOf(Message::class);
    }

    /**
     * Extracts attributes to normalize from the class of the given object, format and context.
     *
     * @param object      $object
     * @param string|null $format
     * @param array       $context
     *
     * @return string[]
     */
    protected function extractAttributes($object, $format = null, array $context = [])
    {
        $rc = new \ReflectionClass($object);

        $attributes = [];
        foreach ($rc->getProperties() as $property) {
            $attributes[] = $property->getName();
        }

        return $attributes;
    }

    /**
     * Gets the attribute value.
     *
     * @param object      $object
     * @param string      $attribute
     * @param string|null $format
     * @param array       $context
     *
     * @return mixed
     */
    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        $value = $this->propertyAccessor->getValue($object, $attribute);

        if (!is_object($value)) {
            return $value;
        }

        $em = $this->registry->getManagerForClass(get_class($value));
        if (!$em) {
            throw new \RuntimeException(sprintf('I do not know how to normalize object of class %s', get_class($value)));
        }

        if ($em) {
            return [
                'type' => get_class($value),
                'id' => $value->getId(),
            ];
        }
    }

    /**
     * Sets attribute value.
     *
     * @param object      $object
     * @param string      $attribute
     * @param mixed       $value
     * @param string|null $format
     * @param array       $context
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        if (is_array($value) && isset($value['type'], $value['id'])) {
            $em = $this->registry->getManagerForClass($value['type']);
            $value = $em->find($value['type'], $value['id']);
        }

        $this->propertyAccessor->setValue($object, $attribute, $value);
    }
}
