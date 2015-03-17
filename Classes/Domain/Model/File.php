<?php
namespace Eww\Dpf\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * File
 */
class File extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * contentType
	 *
	 * @var string
	 */
	protected $contentType = '';

	/**
	 * link
	 *
	 * @var string
	 */
	protected $link = '';

	/**
	 * status
	 *
	 * @var string
	 */
	protected $status;
        
                        
        /**
         * primaryFile
         * 
         * @var boolean
         */
        protected $primaryFile; 
        
        
        /**
         * datastreamIdentifier
         * 
         * @var string
         */
        protected $datastreamIdentifier; 
        
                        
	/**
	 * document
	 *
	 * @var \Eww\Dpf\Domain\Model\Document
	 */
	protected $document = NULL;
        
        const STATUS_REMOTE = "remote";
        const STATUS_DELETED = "deleted";
        const STATUS_CHANGED = "changed";
        const STATUS_ADDED = "added";
        
        const PRIMARY_DATASTREAM_IDENTIFIER = 'FILE_PRIMARY';
        
	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the contentType
	 *
	 * @return string $contentType
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * Sets the contentType
	 *
	 * @param string $contentType
	 * @return void
	 */
	public function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	/**
	 * Returns the link
	 *
	 * @return string $link
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * Sets the link
	 *
	 * @param string $link
	 * @return void
	 */
	public function setLink($link) {
		$this->link = $link;
	}

	/**
	 * Returns the status
	 *
	 * @return boolean $status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Sets the status
	 *
	 * @param boolean $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

                
	/**
	 * Returns the document
	 *
	 * @return \Eww\Dpf\Domain\Model\Document $document
	 */
	public function getDocument() {
		return $this->document;
	}

	/**
	 * Sets the document
	 *
	 * @param \Eww\Dpf\Domain\Model\Document $document
	 * @return void
	 */
	public function setDocument(\Eww\Dpf\Domain\Model\Document $document) {
		$this->document = $document;
	}
        
        
        /**
	 * Returns the primaryFile
	 *
	 * @return boolean $primaryFile
	 */
	public function getPrimaryFile() {
		return $this->primaryFile;
	}
        
        /**
	 * Sets the primaryFile
	 *
	 * @param boolean $primaryFile
	 * @return void
	 */
	public function setPrimaryFile($primaryFile) {
		$this->primaryFile = $primaryFile;
	}
        
	/**
	 * Returns the boolean state of primaryFile
	 *
	 * @return boolean
	 */
	public function isPrimaryFile() {
		return $this->primaryFile;
	}
        
        
        /**
         * Returns the download url for the file
         * 
         * @return string
         */        
        public function getFileUrl() {     
                           
          if ($this->getStatus() == File::STATUS_REMOTE) {            
            return $this->getLink();            
          } else {
            //$baseUrl = $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'];                                               
            return "http://" . trim($_SERVER['SERVER_NAME'],"/")."/".$this->getLink();
          }
         
        }
        
        
        /**
	 * Returns the datastreamIdentifier
	 *
	 * @return string $datastreamIdentifier
	 */
	public function getDatastreamIdentifier() {
		return $this->datastreamIdentifier;
	}

	/**
	 * Sets the datastreamIdentifier
	 *
	 * @param string $datastreamIdentifier
	 * @return void
	 */
	public function setDatastreamIdentifier($datastreamIdentifier) {
		$this->datastreamIdentifier = $datastreamIdentifier;
	}
              
}