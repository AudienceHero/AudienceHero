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

namespace AudienceHero\Bundle\CoreBundle\Tests\Validator\Constraints;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints\RightfullyOwned;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints\RightfullyOwnedValidator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class RightfullyOwnedValidatorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $checker;
    /** @var ObjectProphecy */
    private $context;
    /** @var \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints\RightfullyOwned */
    private $constraint;

    public function setUp()
    {
        $this->dummy = new class() implements OwnableInterface {
            use OwnableEntity;
        };
        $this->checker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->context = $this->prophesize(ExecutionContextInterface::class);
        $this->constraint = new \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints\RightfullyOwned();
    }

    public function testValidatorAbortsIfValueIsNull()
    {
        $this->checker->isGranted(Argument::cetera())->shouldNotBeCalled();
        $this->context->buildViolation($this->constraint->message)->shouldNotBeCalled();

        $validator = new RightfullyOwnedValidator($this->checker->reveal());
        $validator->initialize($this->context->reveal());
        $validator->validate(null, $this->constraint);
    }

    public function testValidatorAbortsIfValueIsNotAnObject()
    {
        $this->checker->isGranted(Argument::cetera())->shouldNotBeCalled();
        $this->context->buildViolation($this->constraint->message)->shouldNotBeCalled();

        $validator = new RightfullyOwnedValidator($this->checker->reveal());
        $validator->initialize($this->context->reveal());
        $validator->validate('foobar', $this->constraint);
    }

    public function testValidatorAbortsIfValueDoesNotImplementOwnableInterface()
    {
        $this->checker->isGranted(Argument::cetera())->shouldNotBeCalled();
        $this->context->buildViolation($this->constraint->message)->shouldNotBeCalled();

        $validator = new RightfullyOwnedValidator($this->checker->reveal());
        $validator->initialize($this->context->reveal());

        $validator->validate(new \stdClass(), $this->constraint);
    }

    public function testValidatorAbortsIfObjectIsRightfullyOwned()
    {
        $this->checker->isGranted(OwnableVoter::ATTRIBUTE, $this->dummy)
                     ->willReturn(true)->shouldBeCalled();

        $this->context->buildViolation($this->constraint->message)->shouldNotBeCalled();

        $validator = new \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints\RightfullyOwnedValidator($this->checker->reveal());
        $validator->initialize($this->context->reveal());

        $validator->validate($this->dummy, $this->constraint);
    }

    public function testValidatorBuildAViolationIfObjectIsNotRightfullyOwner()
    {
        $this->checker->isGranted(OwnableVoter::ATTRIBUTE, $this->dummy)->willReturn(false)->shouldBeCalled();
        $builder = $this->prophesize(ConstraintViolationBuilderInterface::class);
        $builder->addViolation()->shouldBeCalled();
        $this->context->buildViolation($this->constraint->message)->willReturn($builder)->shouldBeCalled();

        $validator = new \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Validator\Constraints\RightfullyOwnedValidator($this->checker->reveal());
        $validator->initialize($this->context->reveal());

        $validator->validate($this->dummy, $this->constraint);
    }
}
