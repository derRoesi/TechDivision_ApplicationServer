<?php
/**
 * This file contains a class made for testing the system for lockingproblems.
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

require_once "Mock/DefinitionTestThread.php";
require_once "Mock/DefinitionTestContainer.php";

class LockingPreventionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the threads within the appserver environment for the problem of locking.
     * Locking: A synchronized method blocks other threads than the lock holding one
     *
     */
    protected $threadA;
    protected $threadB;

    protected $containerA;
    protected $containerB;

    protected $keyA = "ThreadA";
    protected $keyB = "ThreadB";

    protected $mutex;

    public function setUp()
    {
        $this->mutex = \Mutex::create(false);

        $this->containerA = new DefinitionTestContainer();
        $this->containerB = new DefinitionTestContainer();
        $this->threadA = new LockingPreventionTestThread($this->containerA, $this->keyA, $this->mutex, 10);
        $this->threadB = new LockingPreventionTestThread($this->containerB, $this->keyB, $this->mutex, 10);
    }

    public function testLockingPrevention()
    {
        $lockingDetected = false;

        $this->threadA->start();
        $this->threadB->start();

        var_dump($this->containerA);
        var_dump($this->containerB);

        foreach ($this->containerA[$this->keyA] as $timeStampA) {
            foreach ($this->containerB[$this->keyB] as $timeStampB) {
                if ($timeStampA == $timeStampB) {
                    $lockingDetected = true;
                }
            }
        }
        $this->assertFalse($lockingDetected);
    }
}
