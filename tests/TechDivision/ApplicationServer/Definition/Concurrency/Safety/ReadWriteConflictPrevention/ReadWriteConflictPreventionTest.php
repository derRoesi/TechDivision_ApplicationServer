<?php
/**
 * TechDivision\ApplicationServer\ReadWriteConflictPreventionTest
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
require_once "Mock/ReadingThread.php";
require_once "Mock/WritingThread.php";
/**
 *
 * This testcase generates a situation where a thread is reading a resource while another tries to
 * write to the read resource and vice versa.
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
class ReadWriteConflictPreventionTest extends \PHPUnit_Framework_TestCase
{

    protected $readingThread;
    protected $writingThread;

    protected $container;

    protected $key = "ReadWriteTest";
    protected $resultKey = "Result";

    protected $expected;
    protected $expectedEmpty = "";

    protected $ttl = 10;

    public function setUp()
    {
        $this->container = new DefinitionTestContainer();
        $this->readingThread = new ReadingThread($this->container, $this->key, $this->resultKey, $this->ttl);
        $this->writingThread = new WritingThread($this->container, $this->key, $this->ttl);
        $this->buildExpectedResult();
    }

    public function testReadWriteConflictPrevention()
    {
        $this->writingThread->start();
        $this->readingThread->start();

        $this->assertEquals($this->container[$this->resultKey], $this->expected);
    }

    public function testWriteReadConflictPrevention()
    {
        $this->readingThread->start();
        $this->writingThread->start();

        var_dump($this->container);

        $this->assertEquals($this->container[$this->resultKey], $this->expectedEmpty);
    }

    protected function buildExpectedResult()
    {
        for ($i = 0; $i < $this->ttl; $i++) {
            $this->expected .= $i;
        }
    }
}

