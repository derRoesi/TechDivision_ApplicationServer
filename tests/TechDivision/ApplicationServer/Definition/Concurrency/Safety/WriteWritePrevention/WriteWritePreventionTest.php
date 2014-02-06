<?php
/**
 * This file contains the class WriteWriteTest.
 *
 * PHP version 5
 *
 * @category   AppServer
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     René Rösner <r.roesner@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer;

require_once "Mock/DefinitionTestContainer.php";
require_once "Mock/WriteWriteWritingThread.php";

/**
 * TechDivision\ApplicationServer\WriteWriteTest
 *
 * This testcase reproduces the situation of crosswriting threads for testing the failureprevention.
 *
 * @category   AppServer
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     René Rösner <r.roesner@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class WriteWritePreventionTest extends \PHPUnit_Framework_TestCase
{

    protected $threadA;
    protected $threadB;

    protected $container;
    protected $key = "WriteWriteTest";

    protected $signA = "A";
    protected $signB = "B";
    protected $numberOfSignsToBeWroteInARow = 7;

    protected $startingTime;
    protected $delayToThreadStartInSeconds = 1.0;


    public function setUp()
    {
        $mutex = \Mutex::create(false);

        // Initialising threads using the same time to start writing to the resource.
        $this->container = new DefinitionTestContainer();
        $this->startingTime = microtime(true) + $this->delayToThreadStartInSeconds;
        $this->threadA = new WriteWriteWritingThread($this->container, $this->key, $this->signA,
            $this->startingTime, $this->numberOfSignsToBeWroteInARow, $mutex);
        $this->threadB = new WriteWriteWritingThread($this->container, $this->key, $this->signB,
            $this->startingTime, $this->numberOfSignsToBeWroteInARow, $mutex);
    }

    /**
     * Sets two threads in a situation where they are able to crosswrite on a
     */
    public function testWriteWritePrevention()
    {
        // Start threads
        $this->threadA->start();
        $this->threadB->start();
        $this->threadA->join();
        $this->threadB->join();

        //Copy out containeroutput and check for absence of interruption
        $containerOutput = $this->container[$this->key];
        $signsWrittenInARowWithoutInterruption = $this->checkForWritingInterrupts($containerOutput);

        $this->assertTrue($signsWrittenInARowWithoutInterruption);
    }

    /**
     * Checks the containerOutput for the absence of interrupts made by other threads while one thread
     * is writing to a resource. This absence is achieved if one thread was able to
     *
     * @param $containerOutput Content of the container written by the threads
     * @return bool Absence of interuption / Written without failure
     */
    private function checkForWritingInterrupts($containerOutput)
    {
        $i = 0;
        $containerLength = strlen($containerOutput);

        while ($i < $containerLength) {
            $currentChar = $containerOutput[$i];
            for ($j = $i; $j < $this->numberOfSignsToBeWroteInARow; $j++) {
                if ($containerOutput[$j] != $currentChar) {
                    return false;
                }
            }
            // Jumps to the next unchecked section
            $i = $i + $this->numberOfSignsToBeWroteInARow;
        }
        return true;
    }
}

