<?php
/**
 * This file contains the class WaitingPreventionTest.
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
 * TechDivision\ApplicationServer\WaitingPreventionTest
 *
 * This testcase constructs the situation of a waiting problem. This problem causes a blocking situation while one
 * is waiting for another thread to notify.
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

require_once "WaitingTestWaitingThread.php";
require_once "WaitingTestWakingThread.php";
require_once "DefinitionTestContainer.php";
class WaitingPreventionTest extends \PHPUnit_Framework_TestCase
{
    protected $waitingThread;
    protected $waikingThread;

    protected $container;

    public function setUp()
    {
        $this->waitingThread = new WaitingTestWaitingThread();
    }
}
