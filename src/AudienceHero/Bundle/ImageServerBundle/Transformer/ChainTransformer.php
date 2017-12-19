<?php

namespace AudienceHero\Bundle\ImageServerBundle\Transformer;

use Imagine\Imagick\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ChainTransformer.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class ChainTransformer
{
    /** @var array */
    private $transformers = [];
    /** @var OptionsResolver */
    private $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver->setRequired('url');
        $this->resolver->setRequired('content_type');
    }

    public function addTransformer(TransformerInterface $transformer)
    {
        $transformer->configureOptions($this->resolver);
        $priority = $transformer->getPriority();
        if (!isset($this->transformers[$priority])) {
            $this->transformers[$priority] = [$transformer];
        } else {
            $this->transformers[$priority][] = $transformer;
        }
    }

    public function getOptionsResolver(): OptionsResolver
    {
        return $this->resolver;
    }

    public function extractOptions(Request $request): array
    {
        $options = [];
        /** @var array $transformers */
        foreach ($this->transformers as $transformers) {
            /** @var TransformerInterface $transformer */
            foreach ($transformers as $transformer) {
                $options = array_merge($options, $transformer->extractOptions($request));
            }
        }

        return $options;
    }

    public function transform(Image $image, array $options): void
    {
        /** @var array $transformers */
        foreach ($this->transformers as $transformers) {
            /** @var TransformerInterface $transformer */
            foreach ($transformers as $transformer) {
                $transformer->transform($image, $options);
            }
        }
    }
}
