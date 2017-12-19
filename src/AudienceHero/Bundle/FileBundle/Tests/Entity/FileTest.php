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

namespace AudienceHero\Bundle\FileBundle\Tests\Entity;

use AudienceHero\Bundle\FileBundle\Entity\File;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testIsArchive()
    {
        $f = new File();
        $f->setContentType('application/zip');
        $this->assertTrue($f->isArchive());

        $f->setContentType('application/rar');
        $this->assertTrue($f->isArchive());

        $f->setContentType('application/x-rar');
        $this->assertTrue($f->isArchive());

        $f->setContentType('application/x-rar-compressed');
        $this->assertTrue($f->isArchive());

        $f->setContentType('image/jpeg');
        $this->assertFalse($f->isArchive());
    }

    public function testFileValidation()
    {
        $file = new File();
        $file->setContentType('text/plain');

        $validatorBuilder = Validation::createValidatorBuilder();
        $validatorBuilder->enableAnnotationMapping();
        $validator = $validatorBuilder->getValidator();
        $list = $validator->validate($file, null, ['upload']);
        $this->assertCount(1, $list);
        /** @var ConstraintViolationInterface $constraint */
        $constraint = $list[0];
        $this->assertSame('contentType', $constraint->getPropertyPath());
    }
}
