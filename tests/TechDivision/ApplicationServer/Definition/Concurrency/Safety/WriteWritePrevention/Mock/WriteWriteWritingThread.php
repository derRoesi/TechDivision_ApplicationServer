<?php
/**
 * This file contains the class WritingThread.
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

/**
 * TechDivision\ApplicationServer\WritingThread
 *
 * This class describes a thread, that writes to a container.
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
class WriteWriteWritingThread extends \Thread
{
    protected $container;

    protected $key;
    protected $sign;
    protected $numberOfSignsToBeWroteInARow;
    protected $mutex;

    protected $startingTime;
    protected $endingTime;
    protected $runningTime;

    protected $returnMicrotimeAsFloat;

    public function __construct(
        DefinitionTestContainer $container,
        $key,
        $sign,
        $startingTime,
        $numberOfSignsToBeWroteInARow,
        $mutex
    ) {
        // Init
        $this->container = $container;

        /*
         * Init containerkey for using same memory space like the other thread
         * and the sign to be wrote down to resource.
         */
        $this->key = $key;
        $this->sign = $sign;
        $this->numberOfSignsToBeWroteInARow = $numberOfSignsToBeWroteInARow;

        // Defines how long a thread runs and calculates ending time.
        $this->runningTime = 5.0;
        $this->startingTime = $startingTime;
        $this->endingTime = $startingTime + $this->runningTime;

        // Mutex to ensure that a writingprozess is running exclusively.
        $this->returnMicrotimeAsFloat = true;
        $this->mutex = $mutex;
    }

    /**
     * Checks if the runningtime was entered and starts writing to the resource
     * guarded by mutex.
     */
    public function run()
    {
        while (true) {
            if ($this->isInRunningTime()) {
                \Mutex::lock($this->mutex);
                for ($i = 0; $i < $this->numberOfSignsToBeWroteInARow; $i++) {
                    $this->container[$this->key] .= $this->sign;
                }
                \Mutex::unlock($this->mutex);
            } elseif ($this->runningTimeIsOver()) {
                break;
            }
        }
    }

    protected function isInRunningTime()
    {
        $currentTime = microtime($this->returnMicrotimeAsFloat);
        return $this->startingTime <= $currentTime && $currentTime <= $this->endingTime;
    }

    protected function runningTimeIsOver()
    {
        $currentTime = microtime($this->returnMicrotimeAsFloat);
        return $currentTime > $this->endingTime;
    }
}
