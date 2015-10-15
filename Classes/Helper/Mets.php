<?php
namespace EWW\Dpf\Helper;

class Mets {
  
  /**
   * mets
   *
   * @var \DOMDocument
   */
  protected $metsDom;
  
  
  public function __construct($metsXml) {      
    $this->setMetsXml($metsXml);        
  }
  
  
  public function setMetsXml($metsXml) {  
    $metsDom = new \DOMDocument();
    $metsDom->loadXML($metsXml);        
    $this->metsDom = $metsDom;    
  }
  
  
  public function getMetsXml() {
    return $this->metsDom->saveXML(); 
  }
  
       
  public function getMetsXpath() {           
    return new \DOMXPath($this->metsDom); 
  }
  
  
  public function getMods() {
    $xpath = $this->getMetsXpath();
     
    $xpath->registerNamespace("mods", "http://www.loc.gov/mods/v3");        
    $modsNodes = $xpath->query("/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/mods:mods");                                        
            
    $modsXml = $this->metsDom->saveXML($modsNodes->item(0));    
            
    $mods = new Mods($modsXml);
        
    return $mods;
  }
  
  
  public function getSlub() {    
    $xpath = $this->getMetsXpath();
    
    $xpath->registerNamespace("slub", "http://slub-dresden.de/");      
    $slubNodes = $xpath->query("/mets:mets/mets:amdSec/mets:techMD/mets:mdWrap/mets:xmlData/slub:info");  
       
    $slubXml = $this->metsDom->saveXML($slubNodes->item(0)); 
    
    $slub = new Slub($slubXml);
      
    return $slub;
  }
  
    
  public function getFiles() {  
    $xpath = $this->getMetsXpath(); 
    
    $xpath->registerNamespace("xlink", "http://www.w3.org/1999/xlink");   
                                   
    $fileNodes = $xpath->query('/mets:mets/mets:fileSec/mets:fileGrp/mets:file');
          
    $files = array();
    
    foreach ($fileNodes as $item) {     
        
      $xlinkNS = "http://www.w3.org/1999/xlink";
                     
      $files[] = array(
          'id' => $item->getAttribute("ID"),
          'mimetype' => $item->getAttribute("MIMETYPE"),          
          'href' => $item->firstChild->getAttributeNS($xlinkNS,"href"), 
          'title' => $item->firstChild->getAttributeNS($xlinkNS,"title")      
      );      
    }        
             
    return $files;
    
  }
          
}


 

  
  




?>
