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

namespace AudienceHero\Bundle\CoreBundle\Action;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * LanguageListAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class LanguageListAction
{
    /**
     * @var string
     */
    private $localeDir;

    public function __construct(string $localeDir)
    {
        $this->localeDir = $localeDir;
    }

    /**
     * @Route("/api/i18n/{locale}/languages")
     * @Method("GET")
     * @Cache(smaxage=84600)
     *
     * @param string $locale
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     *
     * @return Response
     */
    public function __invoke(Request $request, string $locale)
    {
        $request->setRequestFormat('json');

        $filepath = sprintf('%s/%s/language.json', $this->localeDir, $locale);
        if (!file_exists($filepath)) {
            throw new NotFoundHttpException(sprintf('No country list with locale %s', $locale));
        }

        return new Response(file_get_contents($filepath), 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
