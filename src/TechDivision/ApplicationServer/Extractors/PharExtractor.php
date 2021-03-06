<?php

/**
 * TechDivision\ApplicationServer\Extractors\PharExtractor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Extractors;

use TechDivision\ApplicationServer\AbstractExtractor;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;

/**
 * An extractor implementation for phar files
 *
 * @package TechDivision\ApplicationServer
 * @subpackage Extractors
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license Open Software License (OSL 3.0) http://opensource.org/licenses/osl-3.0.php
 * @author Johann Zelger <j.zelger@techdivision.com>
 */
class PharExtractor extends AbstractExtractor
{

    /**
     * The PHAR identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'phar';

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\AbstractExtractor::getExtensionSuffix()
     */
    public function getExtensionSuffix()
    {
        return '.' . PharExtractor::IDENTIFIER;
    }

    /**
     * Returns the URL for the passed pathname.
     *
     * @param string $pathname
     *            The pathname to return the URL for
     * @return string The URL itself
     */
    public function createUrl($fileName)
    {
        return PharExtractor::IDENTIFIER . '://' . $fileName;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\AbstractExtractor::deployArchive()
     */
    public function deployArchive(\SplFileInfo $archive)
    {
        
        try {
            
            // create folder names based on the archive's basename
            $tmpFolderName = $this->getTmpDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
            $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
            
            // check if archive has not been deployed yet or failed sometime
            if ($this->isDeployable($archive)) {

                // flag webapp as deploying
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYING);
                
                // remove old temporary directory
                $this->removeDir($tmpFolderName);
                
                // backup actual webapp folder, if available
                if (is_dir($webappFolderName)) {
                    $this->backupArchive($archive);
                }
                
                // extract phar to tmp directory
                $p = new \Phar($archive);
                $p->extractTo($tmpFolderName);
                
                // move extracted content to webapps folder
                rename($tmpFolderName, $webappFolderName);
                
                // restore backup if available
                $this->restoreBackup($archive);
                
                // flag webapp as deployed
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYED);
            }
            
        } catch (\Exception $e) {
            
            // log error
            $this->getInitialContext()
                ->getSystemLogger()
                ->error($e->__toString());
            
            // flag webapp as failed
            $this->flagArchive($archive, ExtractorInterface::FLAG_FAILED);
        }
    }

    /**
     * Creates a backup of files that are NOT part of the
     * passed archive.
     *
     * @param \SplFileInfo $archive
     *            Backup files that are NOT part of this archive
     * @return void
     */
    public function backupArchive(\SplFileInfo $archive)
    {
        
        // load the PHAR archive's pathname
        $pharPathname = $archive->getPathname();
        
        // create tmp & webapp folder name based on the archive's basename
        $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
        $tmpFolderName = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(basename($archive->getFilename(), $this->getExtensionSuffix()));
        
        // initialize PHAR archive
        $p = new \Phar($archive);
        
        // iterate over the PHAR content to backup files that are NOT part of the archive
        foreach (new \RecursiveIteratorIterator($p) as $file) {
            unlink(str_replace($this->createUrl($pharPathname), $webappFolderName, $file->getPathName()));
        }
        
        // delete empty directories but LEAVE files created by app
        $this->removeDir($webappFolderName, false);
        
        // copy backup to tmp directory
        $this->copyDir($webappFolderName, $tmpFolderName);
    }
}