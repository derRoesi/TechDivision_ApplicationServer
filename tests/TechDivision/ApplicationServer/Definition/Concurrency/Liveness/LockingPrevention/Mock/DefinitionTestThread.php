<?php
/**
 * This file contains the class DefinitionTestThread.
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
 * TechDivision\ApplicationServer\Test
 *
 * This class describes a thread that was designed for showing the pthreads behavior within the appserver environment
 * while running into a locking problem.
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


class DefinitionTestThread extends \Thread
{
    protected $container;
    protected $key;
    protected $ttl;
    protected $mutex;

    public function __construct(DefinitionTestContainer $container, $key, $mutex, $ttl = 1000)
    {
        $this->container = $container;
        $this->key = $key;
        $this->ttl = $ttl;
        $this->mutex = $mutex;
        $this->container[$key] = array();
    }

    public function run()
    {
        for ($i = 0; $i < $this->ttl; $i++) {
            \Mutex::lock($this->mutex);
            $containerArray = $this->container[$this->key];
            $containerArray[$i] = microtime();
            $this->container[$this->key] = $containerArray;
            \Mutex::unlock($this->mutex);
        }
    }
}
