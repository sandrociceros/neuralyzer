<?php

namespace Edyan\Neuralyzer\Tests;

use Edyan\Neuralyzer\Guesser;
use PHPUnit\Framework\TestCase;

class GuesserTest extends TestCase
{

    public function testInit()
    {
        $guesser = new Guesser;
        $this->assertInstanceOf('Edyan\Neuralyzer\GuesserInterface', $guesser);
        $this->assertInstanceOf('Edyan\Neuralyzer\Guesser', $guesser);
    }

    public function testGetColsNameMapping()
    {
        $guesser = new Guesser;
        $colsNameMapping = $guesser->getColsNameMapping();
        $this->assertInternalType('array', $colsNameMapping);
    }

    public function testGetColsTypeMapping()
    {
        $guesser = new Guesser;
        $colsTypeMapping = $guesser->getColsTypeMapping(null);
        $this->assertInternalType('array', $colsTypeMapping);
    }

    public function testMapColByNameStreetName()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'my_street_name', 'varchar', '255');
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertEquals('streetAddress', $mapping['method']);
    }

    public function testMapColByNameEmail()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'email', 'varchar', '255');
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertEquals('email', $mapping['method']);
    }

    public function testMapColByNameEmail2()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'email_address', 'varchar', '255');
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertEquals('email', $mapping['method']);
    }

    public function testMapColByNameFirstName()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'first_name', 'string', '255');
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertEquals('firstName', $mapping['method']);
    }

    public function testMapColByNameFirstName2()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'firstname', 'string', '255');
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertEquals('firstName', $mapping['method']);
    }

    public function testMapColByType()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'nothingtocompare', 'string', '255');
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertArrayHasKey('params', $mapping);
        $this->assertEquals(255, $mapping['params'][0]);

        // check the version
        $version = $guesser->getVersion();
        $this->assertInternalType('string', $version);
    }

    public function testMapColEnum()
    {
        $guesser = new Guesser;
        $mapping = $guesser->mapCol('test', 'nothingtocompare', 'enum', "'a','b','c'");
        $this->assertInternalType('array', $mapping);
        $this->assertArrayHasKey('method', $mapping);
        $this->assertEquals($mapping['method'], 'randomElement');
        $this->assertArrayHasKey('params', $mapping);
        $this->assertArrayHasKey(0, $mapping['params']);
        $this->assertInternalType('array', $mapping['params'][0]);
        $this->assertEquals('a,b,c', implode(',', $mapping['params'][0]));

        // check the version
        $version = $guesser->getVersion();
        $this->assertInternalType('string', $version);
    }

    /**
     * @expectedException Edyan\Neuralyzer\Exception\NeuralizerGuesserException
     */
    public function testMapColWrongType()
    {
        $guesser = new Guesser;
        $guesser->mapCol('test', 'nothingtocompare', 'nothingtocompare', '255');
    }
}
