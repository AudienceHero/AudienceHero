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

namespace AudienceHero\Bundle\CoreBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Action\LanguageListAction;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageListActionTest extends TestCase
{
    private $action;

    public function setUp()
    {
        $this->action = new LanguageListAction(__DIR__.'/../fixtures/locale');
    }

    public function testActionReturnsCountryListForLocale()
    {
        $action = $this->action;
        $request = new Request();
        $response = $action($request, 'fr');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('français', json_decode($response->getContent(), true)['fr']);
        $this->assertSame('json', $request->getRequestFormat());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testActionReturns404()
    {
        $action = $this->action;
        $request = new Request();
        $response = $action($request, 'zz');
    }
}
