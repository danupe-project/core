<?php

namespace Danupe\Core;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    
    protected string $lineBreakInTestConsole = "\n  ";

    protected function linkeBreakInTestConsoleWithFileInfo(): string
    {
        return $this->lineBreakInTestConsole . $this->lineBreakInTestConsole . 'The error was triggered in class: ' . get_called_class();
    }

    protected function assertAttributeInClassExists(string $attribute, string $classNameWithNamespace)
    {
        $propertyCheck = property_exists($classNameWithNamespace, $attribute);
        if (!$propertyCheck) {
            $this->fail("Attribute: $attribute does not exist in $classNameWithNamespace" . $this->linkeBreakInTestConsoleWithFileInfo());
        } else {
            $this->assertTrue($propertyCheck);
        }
    }

    protected function assertClassExist(string $classNameWithNamespace)
    {
        $classExists = class_exists($classNameWithNamespace);
        if (!$classExists) {
            $this->fail($classNameWithNamespace . " does not exist." . $this->linkeBreakInTestConsoleWithFileInfo());
        } else {
            $this->assertTrue($classExists);
        }
    }

}

