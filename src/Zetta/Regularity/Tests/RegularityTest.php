<?php

namespace Zetta\Regularity\Tests;

use Zetta\Regularity\Regularity;

/**
 * @author zetta
 */
class RegularityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Checking for constants and constructor
     */
    public function testConstructor()
    {
        $regularity = new Regularity();
        $this->assertInstanceOf('Zetta\\Regularity\\Regularity', $regularity);
    }

    /**
     * This test its just to keep the behavior in getExpression and the __toString() method
     */
    public function testStringResult()
    {
        $regularity = new Regularity();

        $regularity
            ->startWith(3, ':digits')
            ->then('-')
            ->then(2, ':letters')
            ->maybe('#')
            ->oneOf(array('a','b'))
            ->between(2,4, 'a')
            ->endWith('$')
        ;

        $this->assertEquals((string) $regularity, $regularity->getExpression() );
    }

    /**
     *
     */
    public function testStartWith()
    {
        $regularity = new Regularity();
        $regularity->startWith('a');
        $this->assertEquals('^a', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->startWith(3, 'x');
        $this->assertEquals('^x{3}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->startWith(3, 'hola');
        $this->assertEquals('^(hola){3}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->startWith(3);
        $this->assertEquals('^3', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->startWith(3, ':digits');
        $this->assertEquals('^[0-9]{3}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->startWith(1, ':digit');
        $this->assertEquals('^[0-9]{1}', $regularity->getExpression());

    }

    /**
     * EndWith test
     */
    public function testEndWith()
    {
        $regularity = new Regularity();
        $regularity->endWith('LOL');
        $this->assertEquals('LOL$', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->endWith(3, ':digits');
        $this->assertEquals('[0-9]{3}$', $regularity->getExpression());
    }

    /**
     * @expectedException Zetta\Regularity\Exception
     */
    public function testThrowExceptionAtStartWith()
    {
        $regularity = new Regularity();
        $regularity
            ->startWith('G')
            ->startWith('Q');
    }

    public function testBuild()
    {
        $regularity = new Regularity();

        $regularity
            ->startWith(3, ':digits')
            ->then('-')
            ->then(2, ':letters')
            ->maybe('#')
            ->oneOf(array('a','b'))
            ->between(2,4, 'a')
            ->endWith('$')
        ;

        $this->assertEquals('^[0-9]{3}-[A-Za-z]{2}#?[a|b]a{2,4}\$$', (string) $regularity);
    }

}