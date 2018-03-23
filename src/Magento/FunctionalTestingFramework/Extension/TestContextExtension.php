<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use \Codeception\Events;

/**
 * Class TestContextExtension
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class TestContextExtension extends \Codeception\Extension
{
    private $failExecutionCount = 0;

    /**
     * Codeception Events Mapping to methods
     * @var array
     */
    public static $events = [
        Events::TEST_BEFORE => 'beforeTest',
        Events::TEST_START => 'startTest',
        Events::TEST_FAIL => 'testFail',
        Events::STEP_AFTER => 'afterStep',
        Events::TEST_AFTER => 'afterTest',
        Events::TEST_ERROR => 'testError',
        Events::RESULT_PRINT_AFTER => 'printResult',
        Events::TEST_FAIL_PRINT => 'printFail'
    ];

    /**
     * Static variable for keeping track of test phase.
     * @var string
     */
    private static $testPhase;

    const TEST_PHASE_BEFORE = "before";
    const TEST_PHASE_TEST = "test";
    const TEST_PHASE_AFTER = "after";

    public function testFail(\Codeception\Event\FailEvent $e)
    {
        print "test failure in context\n";
        $this->failExecutionCount++;

        // once we have our failure we need to execute the after logic (we can assume the test jumps to this block
        // after 1 failure no matter the context) More than one failure would indicated we have had a failure in the
        // test and in the after block. if we are in the after block and haven't had a failure then allow the process
        // to continue executing normally we can do this because we know that we run just 1 test per file
        if ($this->failExecutionCount <= 1) {
            try {
                $actorClazz = $e->getTest()->getMetadata()->getCurrent('actor');
                $cest = $e->getTest();
                $I = new $actorClazz($cest->getScenario());

                call_user_func(\Closure::bind(
                    function () use ($cest, $I) {
                        $cest->executeHook($I, 'after');
                    },
                    null,
                    $cest
                ));
            } catch (\Exception $e) {
                echo 'caught the exception before things got out of hand';
            }
        }
    }

    public function afterStep(\Codeception\Event\StepEvent $e)
    {
        if ($e->getStep()->hasFailed()) {
            print "executing after failed step \n";
        }
    }

    public function startTest(\Codeception\Event\TestEvent $e)
    {
        print "test is starting in context \n";
    }

    public function afterTest(\Codeception\Event\TestEvent $e)
    {
        print "test after in context \n";
    }

    /**
     * Codeception event listener function, triggered on activation of test execution.
     * @return void
     */
    public function beforeTest()
    {
        TestContextExtension::$testPhase = TestContextExtension::TEST_PHASE_BEFORE;
    }

    public function printResult(\Codeception\Event\PrintResultEvent $e)
    {
        print "print Result Context here\n";
    }

    public function printFail(\Codeception\Event\FailEvent $e)
    {
        print "print Fail Context here\n";
    }

    /**
     * Public setter for testPhase
     * @param string $testPhase
     * @return void
     */
    public static function setTestPhase(string $testPhase)
    {
        TestContextExtension::$testPhase = $testPhase;
    }

    public function testError(\Codeception\Event\FailEvent $e)
    {
        print "print Error Context here\n";
    }

    /**
     * Getter for testPhase
     * @return string
     */
    public static function getTestPhase()
    {
        return TestContextExtension::$testPhase;
    }
}
