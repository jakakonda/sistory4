<?php

namespace App\Xsd\Mets\FileType;

/**
 * Class representing FLocatAType
 */
class FLocatAType
{

    /**
     * ID (ID/O): This attribute uniquely identifies the element within the METS
     * document, and would allow the element to be referenced unambiguously from
     * another element or document via an IDREF or an XPTR. For more information on
     * using ID attributes for internal and external linking see Chapter 4 of the METS
     * Primer.
     *
     * @property string $IDAttrName
     */
    private $IDAttrName = null;

    /**
     * LOCTYPE (string/R): Specifies the locator type used in the xlink:href attribute.
     * Valid values for LOCTYPE are: 
     *  ARK
     *  URN
     *  URL
     *  PURL
     *  HANDLE
     *  DOI
     *  OTHER
     *
     * @property string $LOCTYPEAttrName
     */
    private $LOCTYPEAttrName = null;

    /**
     * OTHERLOCTYPE (string/O): Specifies the locator type when the value OTHER is used
     * in the LOCTYPE attribute. Although optional, it is strongly recommended when
     * OTHER is used.
     *
     * @property string $OTHERLOCTYPEAttrName
     */
    private $OTHERLOCTYPEAttrName = null;

    /**
     * USE (string/O): A tagging attribute to indicate the intended use of the specific
     * copy of the file represented by the <FLocat> element (e.g., service master,
     * archive master). A USE attribute can be expressed at the<fileGrp> level, the
     * <file> level, the <FLocat> level and/or the <FContent> level. A USE attribute
     * value at the <fileGrp> level should pertain to all of the files in the
     * <fileGrp>. A USE attribute at the <file> level should pertain to all copies of
     * the file as represented by subsidiary <FLocat> and/or <FContent> elements. A USE
     * attribute at the <FLocat> or <FContent> level pertains to the particular copy of
     * the file that is either referenced (<FLocat>) or wrapped (<FContent>).
     *
     * @property string $USEAttrName
     */
    private $USEAttrName = null;

    /**
     * @property string $typeAttrName
     */
    private $typeAttrName = null;

    /**
     * @property string $hrefPropName
     */
    private $hrefPropName = null;

    /**
     * @property string $rolePropName
     */
    private $rolePropName = null;

    /**
     * @property string $arcrolePropName
     */
    private $arcrolePropName = null;

    /**
     * @property string $titlePropName
     */
    private $titlePropName = null;

    /**
     * @property string $showPropName
     */
    private $showPropName = null;

    /**
     * @property string $actuatePropName
     */
    private $actuatePropName = null;

    /**
     * Gets as IDAttrName
     *
     * ID (ID/O): This attribute uniquely identifies the element within the METS
     * document, and would allow the element to be referenced unambiguously from
     * another element or document via an IDREF or an XPTR. For more information on
     * using ID attributes for internal and external linking see Chapter 4 of the METS
     * Primer.
     *
     * @return string
     */
    public function getIDAttrName()
    {
        return $this->IDAttrName;
    }

    /**
     * Sets a new IDAttrName
     *
     * ID (ID/O): This attribute uniquely identifies the element within the METS
     * document, and would allow the element to be referenced unambiguously from
     * another element or document via an IDREF or an XPTR. For more information on
     * using ID attributes for internal and external linking see Chapter 4 of the METS
     * Primer.
     *
     * @param string $IDAttrName
     * @return self
     */
    public function setIDAttrName($IDAttrName)
    {
        $this->IDAttrName = $IDAttrName;
        return $this;
    }

    /**
     * Gets as LOCTYPEAttrName
     *
     * LOCTYPE (string/R): Specifies the locator type used in the xlink:href attribute.
     * Valid values for LOCTYPE are: 
     *  ARK
     *  URN
     *  URL
     *  PURL
     *  HANDLE
     *  DOI
     *  OTHER
     *
     * @return string
     */
    public function getLOCTYPEAttrName()
    {
        return $this->LOCTYPEAttrName;
    }

    /**
     * Sets a new LOCTYPEAttrName
     *
     * LOCTYPE (string/R): Specifies the locator type used in the xlink:href attribute.
     * Valid values for LOCTYPE are: 
     *  ARK
     *  URN
     *  URL
     *  PURL
     *  HANDLE
     *  DOI
     *  OTHER
     *
     * @param string $LOCTYPEAttrName
     * @return self
     */
    public function setLOCTYPEAttrName($LOCTYPEAttrName)
    {
        $this->LOCTYPEAttrName = $LOCTYPEAttrName;
        return $this;
    }

    /**
     * Gets as OTHERLOCTYPEAttrName
     *
     * OTHERLOCTYPE (string/O): Specifies the locator type when the value OTHER is used
     * in the LOCTYPE attribute. Although optional, it is strongly recommended when
     * OTHER is used.
     *
     * @return string
     */
    public function getOTHERLOCTYPEAttrName()
    {
        return $this->OTHERLOCTYPEAttrName;
    }

    /**
     * Sets a new OTHERLOCTYPEAttrName
     *
     * OTHERLOCTYPE (string/O): Specifies the locator type when the value OTHER is used
     * in the LOCTYPE attribute. Although optional, it is strongly recommended when
     * OTHER is used.
     *
     * @param string $OTHERLOCTYPEAttrName
     * @return self
     */
    public function setOTHERLOCTYPEAttrName($OTHERLOCTYPEAttrName)
    {
        $this->OTHERLOCTYPEAttrName = $OTHERLOCTYPEAttrName;
        return $this;
    }

    /**
     * Gets as USEAttrName
     *
     * USE (string/O): A tagging attribute to indicate the intended use of the specific
     * copy of the file represented by the <FLocat> element (e.g., service master,
     * archive master). A USE attribute can be expressed at the<fileGrp> level, the
     * <file> level, the <FLocat> level and/or the <FContent> level. A USE attribute
     * value at the <fileGrp> level should pertain to all of the files in the
     * <fileGrp>. A USE attribute at the <file> level should pertain to all copies of
     * the file as represented by subsidiary <FLocat> and/or <FContent> elements. A USE
     * attribute at the <FLocat> or <FContent> level pertains to the particular copy of
     * the file that is either referenced (<FLocat>) or wrapped (<FContent>).
     *
     * @return string
     */
    public function getUSEAttrName()
    {
        return $this->USEAttrName;
    }

    /**
     * Sets a new USEAttrName
     *
     * USE (string/O): A tagging attribute to indicate the intended use of the specific
     * copy of the file represented by the <FLocat> element (e.g., service master,
     * archive master). A USE attribute can be expressed at the<fileGrp> level, the
     * <file> level, the <FLocat> level and/or the <FContent> level. A USE attribute
     * value at the <fileGrp> level should pertain to all of the files in the
     * <fileGrp>. A USE attribute at the <file> level should pertain to all copies of
     * the file as represented by subsidiary <FLocat> and/or <FContent> elements. A USE
     * attribute at the <FLocat> or <FContent> level pertains to the particular copy of
     * the file that is either referenced (<FLocat>) or wrapped (<FContent>).
     *
     * @param string $USEAttrName
     * @return self
     */
    public function setUSEAttrName($USEAttrName)
    {
        $this->USEAttrName = $USEAttrName;
        return $this;
    }

    /**
     * Gets as typeAttrName
     *
     * @return string
     */
    public function getTypeAttrName()
    {
        return $this->typeAttrName;
    }

    /**
     * Sets a new typeAttrName
     *
     * @param string $typeAttrName
     * @return self
     */
    public function setTypeAttrName($typeAttrName)
    {
        $this->typeAttrName = $typeAttrName;
        return $this;
    }

    /**
     * Gets as hrefPropName
     *
     * @return string
     */
    public function getHrefPropName()
    {
        return $this->hrefPropName;
    }

    /**
     * Sets a new hrefPropName
     *
     * @param string $hrefPropName
     * @return self
     */
    public function setHrefPropName($hrefPropName)
    {
        $this->hrefPropName = $hrefPropName;
        return $this;
    }

    /**
     * Gets as rolePropName
     *
     * @return string
     */
    public function getRolePropName()
    {
        return $this->rolePropName;
    }

    /**
     * Sets a new rolePropName
     *
     * @param string $rolePropName
     * @return self
     */
    public function setRolePropName($rolePropName)
    {
        $this->rolePropName = $rolePropName;
        return $this;
    }

    /**
     * Gets as arcrolePropName
     *
     * @return string
     */
    public function getArcrolePropName()
    {
        return $this->arcrolePropName;
    }

    /**
     * Sets a new arcrolePropName
     *
     * @param string $arcrolePropName
     * @return self
     */
    public function setArcrolePropName($arcrolePropName)
    {
        $this->arcrolePropName = $arcrolePropName;
        return $this;
    }

    /**
     * Gets as titlePropName
     *
     * @return string
     */
    public function getTitlePropName()
    {
        return $this->titlePropName;
    }

    /**
     * Sets a new titlePropName
     *
     * @param string $titlePropName
     * @return self
     */
    public function setTitlePropName($titlePropName)
    {
        $this->titlePropName = $titlePropName;
        return $this;
    }

    /**
     * Gets as showPropName
     *
     * @return string
     */
    public function getShowPropName()
    {
        return $this->showPropName;
    }

    /**
     * Sets a new showPropName
     *
     * @param string $showPropName
     * @return self
     */
    public function setShowPropName($showPropName)
    {
        $this->showPropName = $showPropName;
        return $this;
    }

    /**
     * Gets as actuatePropName
     *
     * @return string
     */
    public function getActuatePropName()
    {
        return $this->actuatePropName;
    }

    /**
     * Sets a new actuatePropName
     *
     * @param string $actuatePropName
     * @return self
     */
    public function setActuatePropName($actuatePropName)
    {
        $this->actuatePropName = $actuatePropName;
        return $this;
    }

    public function toArray()
    {
        $handleItem = function($item){
            if(is_object($item)){
                if(method_exists($item, "toArray")){
                    return $item->toArray();
                } elseif($item instanceOf \DateTime){
                    return $item->format('Y-m-d\TH:i:s\Z');
                }
                return (string)$item;
            }

            return $item;
        };

        $array = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method){
            if(substr($method, 0, 3) == "get"){
                $var = $this->{$method}();
                $key = substr($method, 3);
                if(is_array($var)){
                    $array[$key] = [];
                    foreach ($var as $k => $itm){
                        $array[$key][$k] = $handleItem($itm);
                    }
                } else {
                    $array[$key] = $handleItem($var);
                }
            }
        }

        return $array;
    }


}

