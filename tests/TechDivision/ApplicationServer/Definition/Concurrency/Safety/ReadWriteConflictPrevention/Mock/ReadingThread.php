<?php
/**
 * This file contains the class ReadingThread.
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
 * This class describes a thread, that reades from a container.
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
class ReadingThread extends \Thread
{
    protected $container;
    protected $key;
    protected $resultKey;
    protected $ttl;

    public function __construct(DefinitionTestContainer $container, $key, $resultKey, $ttl)
    {
        $this->container = $container;
        $this->key = $key;
        $this->resultKey = $resultKey;
        $this->ttl = $ttl;
    }

    public function run()
    {
        $this->container[$this->resultKey] = $this->container[$this->key];
    }

}
