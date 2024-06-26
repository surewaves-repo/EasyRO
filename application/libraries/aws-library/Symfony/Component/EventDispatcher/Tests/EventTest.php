<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Test class for Event.
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\EventDispatcher\Event
     */
    protected $event;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    public function testIsPropagationStopped()
    {
        $this->assertFalse($this->event->isPropagationStopped());
    }

    public function testStopPropagationAndIsPropagationStopped()
    {
        $this->event->stopPropagation();
        $this->assertTrue($this->event->isPropagationStopped());
    }

    public function testSetDispatcher()
    {
        $this->event->setDispatcher($this->dispatcher);
        $this->assertSame($this->dispatcher, $this->event->getDispatcher());
    }

    public function testGetDispatcher()
    {
        $this->assertNull($this->event->getDispatcher());
    }

    public function testGetName()
    {
        $this->assertNull($this->event->getName());
    }

    public function testSetName()
    {
        $this->event->setName('foo');
        $this->assertEquals('foo', $this->event->getName());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->event = new Event();
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->event = null;
        $this->dispatcher = null;
    }
}
