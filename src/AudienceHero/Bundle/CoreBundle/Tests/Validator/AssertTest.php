<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AssertTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Malformed JSON request
     */
    public function testValidJSONRequestThrowsException()
    {
        $request = new Request([], [], [], [], [], [], '{foobar');
        Assert::validJSONRequest($request);
    }

    public function testValidJSONRequest()
    {
        $request = new Request([], [], [], [], [], [], '{}');
        $this->assertTrue(Assert::validJSONRequest($request));
    }
}
