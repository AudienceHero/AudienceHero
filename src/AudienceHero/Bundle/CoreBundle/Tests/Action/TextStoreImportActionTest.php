<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Action\TextStoreImportAction;
use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use AudienceHero\Bundle\CoreBundle\Importer\TextStoreChainImporter;
use AudienceHero\Bundle\CoreBundle\Importer\TextStoreImporterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TextStoreImportActionTest extends TestCase
{
    /** @var TextStore */
    private $importer;

    public function setUp()
    {
        $this->importer = $this->prophesize(TextStoreChainImporter::class);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage No importer available.
     */
    public function testInvokeWithoutImporter()
    {
        $tx = new TextStore();
        $this->importer->getImporterFor($tx)->shouldBeCalled()->willReturn(null);

        $action = new TextStoreImportAction($this->importer->reveal());
        $action($tx);
    }

    public function testInvoke()
    {
        $tx = new TextStore();
        $importer = $this->prophesize(TextStoreImporterInterface::class);
        $importer->import($tx)->shouldBeCalled();

        $this->importer->getImporterFor($tx)->shouldBeCalled()->willReturn($importer->reveal());

        $action = new TextStoreImportAction($this->importer->reveal());
        $response = $action($tx);
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $response);
        $this->assertSame(202, $response->getStatusCode());
    }
}
