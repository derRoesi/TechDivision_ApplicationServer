<?php
/**
 * This file contains the class WaitingTestThread.
 *
 * PHP version 5
 *
 * @category   AppServer
 * @package    TechDivision\ApplicationServer
 * @subpackage Mock
 * @author     René Rösner <r.roesner@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Mock;

use TechDivision\ApplicationServer\DefinitionTestContainer;
use TechDivision\ApplicationServer\WaitingTestWakingThread;

/**
 * TechDivision\ApplicationServer\Mock\WaitingTestThread
 *
 * <REPLACE WITH CLASS DESCRIPTION>
 *
 * @category   AppServer
 * @package    TechDivision\ApplicationServer
 * @subpackage Mock
 * @author     René Rösner <r.roesner@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class WaitingTestWaitingThread extends \Thread
{
    protected $threadToWaitFor;
    protected $container;
    protected $key;

    public function __construct(WaitingTestWakingThread $threadToWaitFor, DefinitionTestContainer $container, $key)
    {
        $this->threadToWaitFor = $threadToWaitFor;
        $this->container = $container;
        $this->key = $key;
    }

    public function run()
    {
        $this->synchronized(
            function () {
                /*
                 * Synchronizes with WakingThread and measures the time
                 * spend during waiting.
                 */
                $startingTime = microtime(true);
                $this->threadToWaitFor->wait();
                $endingTime = microtime(true);

                $waitingTime = $endingTime - $startingTime;

                $this->container[$this->key] = $waitingTime;
            }
        );
    }
}
