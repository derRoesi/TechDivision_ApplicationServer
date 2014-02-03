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
class WritingThread extends \Thread
{
    protected $container;
    protected $key;
    protected $ttl;

    public function __construct(DefinitionTestContainer $container, $key, $ttl)
    {
        $this->container = $container;
        $this->key = $key;
        $this->ttl = $ttl;
    }

    public function run()
    {
        for ($i = 0; $i < $this->ttl; $i++) {
            $this->container[$this->key] .= $i;
        }
    }

}
