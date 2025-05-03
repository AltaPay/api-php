<?php

namespace Altapay\ApiTest;

use Altapay\ApiTest\TestAuthentication;
use Altapay\Exceptions\ClassDoesNotExistsException;
use Altapay\Factory;
use stdClass;

class FactoryTest extends AbstractTest
{
    /**
     * @return list<list<class-string>>
     */
    public function dataProvider()
    {
        $refClass  = new \ReflectionClass(Factory::class);
        $constants = $refClass->getConstants();
        $output    = [];
        foreach ($constants as $class) {
            $this->assertTrue(is_string($class));
            $this->assertTrue(class_exists($class));
            $output[] = [$class];
        }

        return $output;
    }

    /**
     * @dataProvider dataProvider
     *
     * @param        class-string $class
     */
    public function test_can_create($class): void
    {
        $this->assertInstanceOf($class, Factory::create($class, $this->getAuth()));
    }

    public function test_does_not_exists(): void
    {
        $this->expectException(ClassDoesNotExistsException::class);

        /** @var class-string */
        $invalidClassName = $this->noneExistingClass();

        Factory::create($invalidClassName, $this->getAuth());
    }

    public function test_does_not_exists_exception_catch(): void
    {
        /** @var class-string */
        $invalidClassName = $this->noneExistingClass();

        try {
            Factory::create($invalidClassName, $this->getAuth());
        } catch (ClassDoesNotExistsException $e) {
            $this->assertSame($invalidClassName, $e->getClass());
        }
    }
    
    private function noneExistingClass(): string
    {
        return 'Foo\Bar';
    }
}
