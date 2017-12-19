<?php

namespace AudienceHero\Bundle\ImageServerBundle\Transformer;

use Imagine\Imagick\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TransformerInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
interface TransformerInterface
{
    public const PRIORITY_FIRST = 0;
    public const PRIORITY_MIDDLE = 127;
    public const PRIORITY_LAST = 255;

    /**
     * Transform the image.
     *
     * @param Image $image
     * @param array $options
     */
    public function transform(Image $image, array $options): void;

    /**
     * Extract transformer's options from the request.
     *
     * @param Request $request
     * @return array
     */
    public function extractOptions(Request $request): array;

    /**
     * Configure the options the transformer should receive.
     *
     * @param OptionsResolver $options
     * @return mixed
     */
    public function configureOptions(OptionsResolver $options);

    /**
     * Returns the priority at which this transformer should be called.
     * The priority is an integer, ranging from 0 (transformer will be executed first), to 255 (transformer will be executed last)
     * @return int
     */
    public function getPriority(): int;
}
