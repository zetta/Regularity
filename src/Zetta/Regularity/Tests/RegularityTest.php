<?php

namespace Zetta\Regularity\Tests;

use Zetta\Regularity\Regularity;
use Zetta\Regularity\Pattern;
use Zetta\Regularity\Option;

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
        return $regularity;
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
        $regularity->startWith(':digit');
        $this->assertEquals('^[0-9]', $regularity->getExpression());

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

    public function testAppend()
    {
        $regularity = new Regularity();
        $regularity->append('x');
        $this->assertEquals('x', $regularity->getExpression());

        //$regularity = new Regularity();
        $regularity->append(':digit');
        $this->assertEquals('x[0-9]', $regularity->getExpression());

        //$regularity = new Regularity();
        $regularity->append(4, ':letters');
        $this->assertEquals('x[0-9][A-Za-z]{4}', $regularity->getExpression());

        $regularity->append('hola');
        $this->assertEquals('x[0-9][A-Za-z]{4}hola', $regularity->getExpression());

    }

    public function testOneOf()
    {
        $regularity = new Regularity();
        $regularity->oneOf(array(2,4,6));
        $this->assertEquals('(2|4|6)', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->oneOf(array('uno', 'dos'));
        $this->assertEquals('(uno|dos)', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->oneOf(array(':digit',':letter'));
        $this->assertEquals('([0-9]|[A-Za-z])', $regularity->getExpression());

        $this->setExpectedException('PHPUnit_Framework_Error'); // error because we cant send string when we need an array
        $regularity = new Regularity();
        $regularity->oneOf("a");
    }

    /**
     *
     */
    public function testBetween()
    {
        $regularity = new Regularity();
        $regularity->between(2, 4, ':digit');
        $this->assertEquals('[0-9]{2,4}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->between(2, 4, 'x');
        $this->assertEquals('x{2,4}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->between(2,4, 'qqq');
        $this->assertEquals('(qqq){2,4}', $regularity->getExpression());
    }

    public function testAtLeast()
    {
        //$this->markTestIncomplete('This test has not been implemented yet.');
        $regularity = new Regularity();
        $regularity->atLeast(2, ':digit');
        $this->assertEquals('[0-9]{2,}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->atLeast(9, 'a');
        $this->assertEquals('a{9,}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->atLeast(9, 'abc');
        $this->assertEquals('(abc){9,}', $regularity->getExpression());
    }

    public function testAtMost()
    {
        $regularity = new Regularity();
        $regularity->atMost(2, ':digit');
        $this->assertEquals('[0-9]{,2}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->atMost(9, 'a');
        $this->assertEquals('a{,9}', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->atMost(9, 'abc');
        $this->assertEquals('(abc){,9}', $regularity->getExpression());
    }

    public function testMaybe()
    {
        $regularity = new Regularity();
        $regularity->maybe('A');
        $this->assertEquals('A?', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->maybe(':digit');
        $this->assertEquals('[0-9]?', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->maybe(4, ':digits');
        $this->assertEquals('([0-9]{4})?', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->maybe(4, 'hola');
        $this->assertEquals('((hola){4})?', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->maybe('hola');
        $this->assertEquals('(hola)?', $regularity->getExpression());
    }

    public function testZeroOrMore()
    {
        $regularity = new Regularity();
        $regularity->zeroOrMore('A');
        $this->assertEquals('A*', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->zeroOrMore(':digit');
        $this->assertEquals('[0-9]*', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->zeroOrMore(4, ':digits');
        $this->assertEquals('([0-9]{4})*', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->zeroOrMore(4, 'hola');
        $this->assertEquals('((hola){4})*', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->zeroOrMore('hola');
        $this->assertEquals('(hola)*', $regularity->getExpression());
    }

    public function testOneOrMore()
    {
        $regularity = new Regularity();
        $regularity->oneOrMore('A');
        $this->assertEquals('A+', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->oneOrMore(':digit');
        $this->assertEquals('[0-9]+', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->oneOrMore(4, ':digits');
        $this->assertEquals('([0-9]{4})+', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->oneOrMore(4, 'hola');
        $this->assertEquals('((hola){4})+', $regularity->getExpression());

        $regularity = new Regularity();
        $regularity->oneOrMore('hola');
        $this->assertEquals('(hola)+', $regularity->getExpression());
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

        $regularity = new Regularity();
        $regularity->endWith(3, 'xxx');
        $this->assertEquals('(xxx){3}$', $regularity->getExpression());
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

    /**
     * Complete Build!!
     */
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

        $this->assertEquals('^[0-9]{3}\-[A-Za-z]{2}#?(a|b)a{2,4}\$$', (string) $regularity);
        return $regularity;
    }

    /**
     * @depends testBuild
     */
    public function testTest(Regularity $regularity)
    {
        $this->assertTrue($regularity->test('123-Abaaa$'));
        $this->assertTrue($regularity->test('123-Ab#aaaaa$'));
        $this->assertFalse($regularity->test('1234-Ab#aaa$'));
        $this->assertFalse($regularity->test('u123-Abaaa$'));

        $regularity = new Regularity();
        $regularity
            ->startWith(2, ':letters')
            ->then('/')
            ->append(6, ':alphanumeric')
        ;
        $this->assertTrue($regularity->test('ab/abc123'));
    }

    /**
     *
     */
    public function testModifiers()
    {
        $regularity = new Regularity();
        $regularity
            ->startWith(3, Pattern::LOWERCASE)
            ->then(4, Pattern::UPPERCASE)
            ->endWith(2, Pattern::DIGITS)
        ;
        $this->assertTrue($regularity->test('aaaBBBB88'));
        $this->assertFalse($regularity->test('AaaBBBB88'));

        $regularity->ignoreCase();

        $this->assertTrue($regularity->test('AaaBBBB88'));
    }

}