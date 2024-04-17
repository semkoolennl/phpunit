<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject;

use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Generator\RuntimeException as GeneratorRuntimeException;
use PHPUnit\Framework\MockObject\Generator\UnknownTypeException;
use PHPUnit\Framework\TestCase;
use PHPUnit\TestFixture\MockObject\AnInterface;
use PHPUnit\TestFixture\MockObject\AnotherInterface;
use PHPUnit\TestFixture\MockObject\AnotherInterfaceThatDoesSomething;

#[Group('test-doubles')]
#[Group('test-doubles/creation')]
#[Group('test-doubles/mock-object')]
#[Medium]
#[TestDox('createMockForIntersectionOfInterfaces()')]
#[CoversMethod(TestCase::class, 'createMockForIntersectionOfInterfaces')]
final class CreateMockForIntersectionOfInterfacesTest extends TestCase
{
    public function testCreatesMockObjectForIntersectionOfInterfaces(): void
    {
        $double = $this->createMockForIntersectionOfInterfaces([AnInterface::class, AnotherInterface::class]);

        $this->assertInstanceOf(AnInterface::class, $double);
        $this->assertInstanceOf(AnotherInterface::class, $double);
        $this->assertInstanceOf(Stub::class, $double);
    }

    public function testReturnValueGenerationIsEnabledByDefault(): void
    {
        $double = $this->createMockForIntersectionOfInterfaces([AnInterface::class, AnotherInterface::class]);

        $this->assertFalse($double->doSomething());
        $this->assertNull($double->doSomethingElse());
    }

    public function testCannotCreateMockObjectForIntersectionOfInterfacesWhenLessThanTwoInterfacesAreSpecified(): void
    {
        $this->expectException(GeneratorRuntimeException::class);
        $this->expectExceptionMessage('At least two interfaces must be specified');

        $this->createMockForIntersectionOfInterfaces([AnInterface::class]);
    }

    public function testCannotCreateMockObjectForIntersectionOfUnknownInterfaces(): void
    {
        $this->expectException(UnknownTypeException::class);

        $this->createMockForIntersectionOfInterfaces(['DoesNotExist', 'DoesNotExist']);
    }

    public function testCannotCreateMockObjectForIntersectionOfInterfacesThatDeclareTheSameMethod(): void
    {
        $this->expectException(GeneratorRuntimeException::class);
        $this->expectExceptionMessage('Interfaces must not declare the same method');

        $this->createMockForIntersectionOfInterfaces([AnInterface::class, AnotherInterfaceThatDoesSomething::class]);
    }
}
