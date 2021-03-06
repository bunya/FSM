<?php

namespace FSM\Machine;

use FSM\ContextInterface;
use FSM\Event\EventFactoryInterface;
use FSM\Event\EventInterface;
use FSM\FSMLocator;
use FSM\State\StateFactoryInterface;
use FSM\State\StateInterface;
use FSM\Transition\TransitionInterface;
use FSM\Transition\TransitionTableInterface;

/**
 * State machine test
 */
class MachineTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSignalAllowed()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $stateFactoryMock */
        $stateFactoryMock = $this->getStateFactoryMock();
        $stateFactoryMock
            ->expects($this->any())
            ->method('getState')
            ->willReturn($this->getStateMock());

        /** @var \PHPUnit_Framework_MockObject_MockObject $transitionTableMock */
        $transitionTableMock = $this->getTransitionTableMock();
        $transitionTableMock
            ->expects($this->any())
            ->method('findTransitions')
            ->willReturn([
                $this->getTransitionMock(),
            ]);

        /** @var TransitionTableInterface $transitionTableMock */
        /** @var StateFactoryInterface $stateFactoryMock */
        $machine = new Machine(
            'test_name',
            $transitionTableMock,
            $stateFactoryMock,
            $this->getEventFactoryMock(),
            [FSMLocator::OPTIONS_KEY_STRICT => false]
        );

        $test = $machine->isSignalAllowed($this->getContextMock(), 'test_signal');
        $this->assertInternalType('boolean', $test);
        $this->assertTrue($test);
    }

    public function testIsSignalAllowedStrictOption()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $stateFactoryMock */
        $stateFactoryMock = $this->getStateFactoryMock();
        $stateFactoryMock
            ->expects($this->any())
            ->method('getState')
            ->willReturn($this->getStateMock());

        /** @var \PHPUnit_Framework_MockObject_MockObject $transitionTableMock */
        $transitionTableMock = $this->getTransitionTableMock();
        $transitionTableMock
            ->expects($this->any())
            ->method('findTransitions')
            ->willReturn([
                $this->getTransitionMock(),
                $this->getTransitionMock(),
            ]);

        /** @var TransitionTableInterface $transitionTableMock */
        /** @var StateFactoryInterface $stateFactoryMock */
        $machine = new Machine(
            'test_name',
            $transitionTableMock,
            $stateFactoryMock,
            $this->getEventFactoryMock(),
            [FSMLocator::OPTIONS_KEY_STRICT => false]
        );

        $this->assertTrue($machine->isSignalAllowed($this->getContextMock(), 'test_signal'));

        $machine = new Machine(
            'test_name',
            $transitionTableMock,
            $stateFactoryMock,
            $this->getEventFactoryMock(),
            [FSMLocator::OPTIONS_KEY_STRICT => Machine::STRICT_SIMULTANEOUS_SIGNAL_TRANSITIONS]
        );

        $this->setExpectedException(Exception\StrictException::class);
        $this->assertTrue($machine->isSignalAllowed($this->getContextMock(), 'test_signal'));
    }

    /**
     * @todo
     */
    public function testPerform()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $stateFactoryMock */
        $stateFactoryMock = $this->getStateFactoryMock();
        $stateFactoryMock
            ->expects($this->any())
            ->method('getState')
            ->willReturn($this->getStateMock());

        /** @var \PHPUnit_Framework_MockObject_MockObject $transitionMock */
        $transitionMock = $this->getTransitionMock();

        $testCount = 0;
        /** @var \PHPUnit_Framework_MockObject_MockObject $transitionTableMock */
        $transitionTableMock = $this->getTransitionTableMock();
        $transitionTableMock
            ->expects($this->once())
            ->method('findTransitions')
            ->willReturn(++$testCount > 0 ? [] : [$transitionMock]);

        /** @var TransitionTableInterface $transitionTableMock */
        /** @var StateFactoryInterface $stateFactoryMock */
        $machine = new Machine(
            'test_name',
            $transitionTableMock,
            $stateFactoryMock,
            $this->getEventFactoryMock(),
            [FSMLocator::OPTIONS_KEY_STRICT => false]
        );

        $machine->refresh($this->getContextMock());
    }

    /**
     * @todo
     */
    public function testSignal()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $stateFactoryMock */
        $stateFactoryMock = $this->getStateFactoryMock();
        $stateFactoryMock
            ->expects($this->any())
            ->method('getState')
            ->willReturn($this->getStateMock());

        /** @var \PHPUnit_Framework_MockObject_MockObject $transitionMock */
        $transitionMock = $this->getTransitionMock();

        $testCount = 0;
        /** @var \PHPUnit_Framework_MockObject_MockObject $transitionTableMock */
        $transitionTableMock = $this->getTransitionTableMock();
        $transitionTableMock
            ->expects($this->any())
            ->method('findTransitions')
            ->willReturn(++$testCount > 0 ? [] : [$transitionMock]);

        /** @var TransitionTableInterface $transitionTableMock */
        /** @var StateFactoryInterface $stateFactoryMock */
        $machine = new Machine(
            'test_name',
            $transitionTableMock,
            $stateFactoryMock,
            $this->getEventFactoryMock(),
            [FSMLocator::OPTIONS_KEY_STRICT => false]
        );

        $machine->signal($this->getContextMock(), 'test_signal');
    }

    /**
     * @return TransitionTableInterface
     */
    private function getTransitionTableMock()
    {
        $mock = $this->getMock(
            TransitionTableInterface::class,
            ['findTransitions']
        );

        return $mock;
    }

    /**
     * @return StateFactoryInterface
     */
    private function getStateFactoryMock()
    {
        $mock = $this->getMock(
            StateFactoryInterface::class,
            ['getState']
        );

        return $mock;
    }

    /**
     * @return EventFactoryInterface
     */
    private function getEventFactoryMock()
    {
        $mock = $this->getMock(
            EventFactoryInterface::class,
            ['getEvent', 'dispatchEvent']
        );

        $mock
            ->expects($this->any())
            ->method('getEvent')
            ->willReturn($this->getEventMock());

        return $mock;
    }

    /**
     * @return ContextInterface
     */
    private function getContextMock()
    {
        $mock = $this->getMock(
            ContextInterface::class,
            ['getContextUid', 'getContextState', 'setContextState']
        );

        $mock
            ->expects($this->any())
            ->method('getContextUid')
            ->willReturn('TestUID');

        return $mock;
    }

    /**
     * @return StateInterface
     */
    private function getStateMock()
    {
        $mock = $this->getMock(
            StateInterface::class,
            ['getName', 'getType', 'isIdentical']
        );

        return $mock;
    }

    /**
     * @return StateInterface
     */
    private function getAnotherStateMock()
    {
        $mock = $this->getMock(
            StateInterface::class,
            ['getName', 'getType', 'isIdentical']
        );

        return $mock;
    }

    /**
     * @return TransitionInterface
     */
    private function getTransitionMock()
    {
        $mock = $this->getMock(
            TransitionInterface::class,
            ['getStateFrom', 'getStateTo', 'isDirect', 'getSignal', 'hasGuard', 'getGuard']
        );

        $mock
            ->expects($this->any())
            ->method('getStateTo')
            ->willReturn($this->getAnotherStateMock());

        return $mock;
    }

    /**
     * @return EventInterface
     */
    private function getEventMock()
    {
        $mock = $this->getMock(
            EventInterface::class
        );

        return $mock;
    }
}
