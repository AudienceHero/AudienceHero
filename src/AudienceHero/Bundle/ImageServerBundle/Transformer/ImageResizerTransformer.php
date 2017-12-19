<?php

namespace AudienceHero\Bundle\ImageServerBundle\Transformer;

use Imagine\Image\Box;
use Imagine\Imagick\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * ImageResizerTransformer.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class ImageResizerTransformer implements TransformerInterface
{
    public function transform(Image $image, array $options): void
    {
        if ($options['content_type'] === 'image/gif') {
            return;
        }

        if (0 === $options['height']) {
            $image->resize($image->getSize()->widen($options['width']));

            return;
        }

        if (0 === $options['width']) {
            $image->resize($image->getSize()->heighten($options['height']));

            return;
        }

        $image->resize(new Box($options['width'], $options['height']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['size', 'width', 'height']);
        $resolver->setAllowedTypes('size', ['null', 'string']);
        $resolver->setAllowedTypes('width', ['null', 'int']);
        $resolver->setAllowedTypes('height', ['null', 'int']);

        $resolver->setNormalizer('width', function(Options $options, ?int $value) {
            if ($value) {
                return $value;
            }

            $size = $options['size'];
            if (!$size) {
                throw new MissingOptionsException('The \'size\' or \'width\' option must be defined', ['size', 'width']);
            }

            return explode('x', $size)[0] ?: 0;
        });

        $resolver->setNormalizer('height', function(Options $options, ?int $value) {
            if ($value) {
                return $value;
            }

            $size = $options['size'];
            if (!$size) {
                throw new MissingOptionsException('The \'size\' or \'height\' option must be defined', ['size', 'height']);
            }

            return explode('x', $size)[1] ?: 0;
        });
    }

    public function extractOptions(Request $request): array
    {
        if ($size = $request->query->get('amp;size')) {
            $request->query->set('size', $size);
            $request->query->remove('amp;size');
        }

        return [
            'size' => $request->query->get('size'),
            'height' => $request->query->get('height'),
            'width' => $request->query->get('width'),
        ];
    }

    public function getPriority(): int
    {
        return TransformerInterface::PRIORITY_FIRST;
    }
}
