<?php

namespace App\Models;

use App\Helpers\DcHelpers;
use App\Helpers\EntitySelect;
use App\Helpers\FileHelpers;
use App\Helpers\Si4Util;
use App\Helpers\Timer;
use App\Helpers\XmlHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


/**
 * App\Models\Entity
 *
 * @property int $id
 * @property string $handle_id
 * @property string $parent
 * @property string $primary
 * @property string $collection
 * @property string $struct_type
 * @property string $struct_subtype
 * @property string $entity_type
 * @property int $child_order
 * @property string $xml
 * @property boolean $active
 * @property boolean $req_text_reindex
 * @property boolean $req_thumb_regen
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Entity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Entity whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Entity whereStructTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Entity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Entity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Entity extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'handle_id',
        'parent',
        'primary',
        'collection',
        'struct_type',
        'struct_subtype',
        'entity_type',
        'child_order',
        'xml',
        'active',
        'req_text_reindex',
        'req_thumb_regen'
    ];

    /**
     * @return bool
     */
    // public function dataSchemaValidate() : bool
    public function dataSchemaValidate()
    {
        return self::xmlStringSchemaValidate($this->xml, $this->struct_type);
    }

    /*
    public function dataToObject()
    {
        if (!$this->data) return null;

        Timer::start("xmlParsing");

        $serializerBuilder = SerializerBuilder::create();

        $serializerBuilder->addMetadataDir(app_path("Xsd/Mets"), 'App\Xsd\Mets');
        $serializerBuilder->addMetadataDir(app_path("Xsd/Dc"), 'App\Xsd\Dc');
        $serializerBuilder->addMetadataDir(app_path("Xsd/Premis"), 'App\Xsd\Premis');
        $serializerBuilder->addMetadataDir(app_path("Xsd/Mods"), 'App\Xsd\Mods');
        $serializerBuilder->addMetadataDir(app_path("Xsd/Entity"), 'App\Xsd\Entity');
        $serializerBuilder->addMetadataDir(app_path("Xsd/Collection"), 'App\Xsd\Collection');

        $serializerBuilder->configureHandlers(function (HandlerRegistryInterface $handler) use ($serializerBuilder) {
            $serializerBuilder->addDefaultHandlers();
            $handler->registerSubscribingHandler(new BaseTypesHandler()); // XMLSchema List handling
            $handler->registerSubscribingHandler(new XmlSchemaDateHandler()); // XMLSchema date handling

            //$handler->registerSubscribingHandler(new XsiTypeHandler());

            //$handler->registerSubscribingHandler(new XmlDataATypeHandler());
            //$handler->registerSubscribingHandler(new AnySimpleTypeHandler());
            //$handler->registerSubscribingHandler(new DcTypeHandler());

            //$handler->registerSubscribingHandler(new Base64TypeHandler());
            //$handler->registerSubscribingHandler(new AnyTypeHandler());
            //$handler->registerSubscribingHandler(new AsTextTypeHandler());

        });

        $serializer = $serializerBuilder->build();


        // deserialize the XML into object
        $object = $serializer->deserialize($this->data, 'App\Xsd\Mets\Mets', 'xml');
        //print_r($object);

        $array = $object->toArray();
        //print_r($array);

        Timer::stop("xmlParsing");

        return $array;
    }
    */


    /*
    var templateReplaceMap = {
        systemId: data.id,
        handleId: data.handleId,
        si4id: "si4."+data.handleId,
        handleUrl: "http://hdl.handle.net/"+si4.data.repositoryInfo.handlePrefix+"/"+data.handleId,
        structType: data.structType,
        currentTimestamp: si4.dateToISOString(),
        recordStatus: "Active",
        repositoryName: si4.data.repositoryInfo.name,
        repositoryNote: si4.data.repositoryInfo.note,
        userId: si4.data.currentUser.id,
        userFullname: si4.data.currentUser.lastname +", "+ si4.data.currentUser.firstname,
    };

     {{systemId}}         - Id, ki ga vedno dodeli sistem
     {{handleId}}         - Handle prefix (se lahko ročno vnese le ob kreiranju)
     {{si4id}}            - konstanta "si4." + handleId, npr. "si4.entity123"
     {{handleUrl}}        - Handle url
     {{structType}}       - tip (collection/entity/file)
     {{currentTimestamp}} - trenutni datum čas, npr. "2018-12-10T20:11:13"
     {{recordStatus}}     - "Active"
     {{repositoryName}}   - Ime repozitorija
     {{repositoryNote}}   - Url repozitorija
     {{userId}}           - Id prijavljenega uporabnika
     {{userFullname}}     - Polno ime prijavljenega uporabnika

    */

    private static $doNotTouch_metsFileUses = ["EXTERNAL", "YOUTUBE"];

    public function updateXml() {

        $currentUser = \Auth::user();

        Timer::start("xmlParsing");

        $xmlDoc = simplexml_load_string($this->xml);
        $xmlDoc['ID'] = "si4.".$this->handle_id;
        $xmlDoc['OBJID'] = "http://hdl.handle.net/".si4config("handlePrefix")."/".$this->handle_id;
        $xmlDoc['TYPE'] = $this->struct_type;

        // * MetsHdr
        $metsHdr = $xmlDoc->xpath("METS:metsHdr")[0];
        $metsHdr["LASTMODDATE"] = DcHelpers::dateToISOString();
        $metsHdr["RECORDSTATUS"] = $this->active ? "Active" : "Inactive";

        $agentDisseminatorArr = $metsHdr->xpath("METS:agent[@ROLE='DISSEMINATOR']");
        if (!count($agentDisseminatorArr)) {
            $agentDisseminator = $metsHdr->addChild("METS:agent");
            $agentDisseminator["ROLE"] = "DISSEMINATOR";
            $agentDisseminator["TYPE"] = "ORGANIZATION";
            $agentDisseminatorName = $agentDisseminator->addChild("METS:name");
            $agentDisseminatorName[0] = si4config("siteName");
            $agentDisseminatorNote = $agentDisseminator->addChild("METS:note");
            $agentDisseminatorNote[0] = si4config("siteUrl");
        }

        $agentCreatorArr = $metsHdr->xpath("METS:agent[@ROLE='CREATOR']");
        if (!count($agentCreatorArr)) {
            $agentCreator = $metsHdr->addChild("METS:agent");
            $agentCreator["ROLE"] = "CREATOR";
            $agentCreator["ID"] = $currentUser->id;
            $agentCreator["TYPE"] = "INDIVIDUAL";
            $agentCreatorName = $agentCreator->addChild("METS:name");
            $agentCreatorName[0] = $currentUser->lastname.", ".$currentUser->firstname;
        }

        // * DmdSec

        // * AmdSec

        // Premis
        $premisXmlData = $xmlDoc->xpath("METS:amdSec/METS:techMD/METS:mdWrap[@MDTYPE='PREMIS:OBJECT']/METS:xmlData")[0];

        $premisIdentifiers = $premisXmlData->xpath("premis:objectIdentifier");
        foreach($premisIdentifiers as $idx => $premisIdentifier) {
            $piTypeNode = $premisIdentifier->xpath("premis:objectIdentifierType")[0];
            $piValueNode = $premisIdentifier->xpath("premis:objectIdentifierValue")[0];
            $piType = (string)$piTypeNode;

            if ($piType == "si4") {
                $piValueNode[0] = $this->id;
            }
            else if ($piType == "Local name") {
                $piValueNode[0] = $this->handle_id;
            }
            else if ($piType == "hdl") {
                $piValueNode[0] = "http://hdl.handle.net/".si4config("handlePrefix")."/".$this->handle_id;
            }
        }

        $premisObjCategory = $premisXmlData->xpath("premis:objectCategory")[0];
        switch ($this->struct_type) {
            case "collection": $premisObjCategory[0] = "Collection"; break;
            case "entity": $premisObjCategory[0] = "Intellectual entity"; break;
            case "file": $premisObjCategory[0] = "File"; break;
        }


        // Load relations
        $childrenData = $this->findChildren();
        $children = $childrenData["data"];

        $parentHierarchy = $this->findParentHierarchy();
        $parents = $parentHierarchy["data"];

        if ($this->struct_type == "entity") {

            // *** Entity struct type ***

            // * fileSec

            $fileSecArr = $xmlDoc->xpath("METS:fileSec");
            if (count($fileSecArr)) $fileSec = $fileSecArr[0];
            else $fileSec = $xmlDoc->addChild("METS:fileSec");

            $fileSec["ID"] = "files";
            $fileSecGrp = null;

            // Remove METS:fileSec/METS:fileGrp and reconstruct
            $fileSecGrpArray = $xmlDoc->xpath("METS:fileSec/METS:fileGrp");
            if (count($fileSecGrpArray)) {
                for ($i = 0; $i < count($fileSecGrpArray); $i++) {
                    if (strtoupper($fileSecGrpArray[$i]["USE"]) == "DEFAULT") {
                        $fileSecGrp = $fileSecGrpArray[$i];
                        break;
                    }
                }
            }
            if (!$fileSecGrp) $fileSecGrp = $fileSec->addChild("METS:fileGrp");
            $fileSecGrp["USE"] = "DEFAULT";

            $existingMetsFiles = $fileSecGrp->xpath("METS:file");
            for ($i = 0; $i < count($existingMetsFiles); $i++) {
                $use = strtoupper($existingMetsFiles[$i]["USE"]);

                // Remove only METS:file if its "use" attribute is not in the doNotTouch array.
                if (!in_array($use, self::$doNotTouch_metsFileUses)) {
                    unset($existingMetsFiles[$i][0]);
                }
            }

            //print_r($children);
            foreach ($children as $child) {
                if ($child["struct_type"] !== "file") continue;
                //print_r($child);

                $fileSecFile = $fileSecGrp->addChild("METS:file");
                $fileSecFile["ID"] = $child["handle_id"];
                $fileSecFile["OWNERID"] = $child["fileName"];
                $fileSecFile["USE"] = $child["struct_subtype"];

                $fileSecFileLocat = $fileSecFile->addChild("METS:FLocat");
                $fileSecFileLocat["LOCTYPE"] = "HANDLE";
                $fileSecFileLocat["xlink:href"] = "http://hdl.handle.net/".si4config("handlePrefix")."/".$child["handle_id"];
            }

            //print_r($fileSec->asXML());

            //<METS:file ID="" OWNERID="" USE="">
                //<!-- Atribut xlink:href vsebuje handle te datoteke (npr. https://hdl.handle.net/11686/file22731) -->
            //    <METS:FLocat LOCTYPE="HANDLE" xlink:href=""/>
            //</METS:file>

            //unset($xmlDoc->xpath("METS:fileSec/METS:fileGrp/METS:file")[0][0]);

                //unset($fileSecGrp[0][0]);

            // METS:fileSec/METS:fileGrp
            //$fileSecGrp = $fileSec->addChild("METS:fileGrp");
            //print_r($children);

        } else if ($this->struct_type == "file") {

            // *** File struct type ***
            // File attributes

            $fileSecArr = $xmlDoc->xpath("METS:fileSec");
            if (count($fileSecArr)) $fileSec = $fileSecArr[0];
            else $fileSec = $xmlDoc->addChild("METS:fileSec");

            $fileSecGrpArray = $fileSec->xpath("METS:fileGrp");
            if (count($fileSecGrpArray)) $fileSecGrp = $fileSecGrpArray[0];
            else $fileSecGrp = $fileSec->addChild("METS:fileGrp");

            $fileSecFileArray = $fileSecGrp->xpath("METS:file");
            if (count($fileSecFileArray)) $metsFile = $fileSecFileArray[0];
            else $metsFile = $fileSec->addChild("METS:file");

            //$metsFile = $xmlDoc->xpath("METS:fileSec/METS:fileGrp/METS:file")[0];
            $fileName = $metsFile["OWNERID"];
            $parent = $this->parent;
            $storageName = FileHelpers::getPublicStorageName($parent, $fileName);

            if (Storage::exists($storageName)) {
                $fullFilePath = storage_path('app')."/".$storageName;
                $metsFile["MIMETYPE"] = Storage::mimeType($storageName); // FileHelpers::fileNameMime($fileName);
                $metsFile["SIZE"] = Storage::size($storageName);
                $metsFile["CREATED"] = date("Y-m-d\\TH:i:s\\Z", filemtime($fullFilePath));
                $metsFile["CHECKSUM"] = md5_file($fullFilePath);
                $metsFile["CHECKSUMTYPE"] = "MD5";
            }
            //print_r($metsFile);
        }




        // * structMap

        // METS:structMap
        $structMapArr = $xmlDoc->xpath("METS:structMap");
        if (count($structMapArr)) $structMap = $structMapArr[0];
        else $structMap = $xmlDoc->addChild("METS:structMap");


        if (!count($parents)) {
            // No parents.
            $structMap["TYPE"] = "primary";
        } else {
            $parent = $parents[count($parents) -1];

            $structMap["ID"] = "structure";
            //$structMap["TYPE"] = $parent["entity_type"];
            $structMap["TYPE"] = $this->entity_type;

            // Remove METS:structMap/METS:div and reconstruct
            unset($xmlDoc->xpath("METS:structMap/METS:div")[0][0]);


            // METS:structMap/METS:div - parentDiv
            $structParentDiv = $structMap->addChild("METS:div");
            $structParentDiv["TYPE"] = $parent["struct_type"];

            // METS:structMap/METS:div/METS:mptr - parent mptr
            $structParentMptr = $structParentDiv->addChild("METS:mptr");
            $structParentMptr["LOCTYPE"] = "HANDLE";
            $structParentMptr["xlink:href"] = "http://hdl.handle.net/".si4config("handlePrefix")."/".$this->parent;

            // METS:structMap/METS:div/METS:div - currentDiv
            $structCurrentDiv = $structParentDiv->addChild("METS:div");
            $structCurrentDiv["TYPE"] = $this->struct_type;
            //$structCurrentDiv["DMDID"] = "default.dc default.mods";
            //$structCurrentDiv["AMDID"] = "default.amd";

            //$children = $hierarchy["data"]["children"];
            //print_r(array_keys($children[0]));
            //print_r($children[0]["handle_id"]);
            foreach ($children as $child) {
                $childHandleId = $child["handle_id"];
                $childStructType = $child["struct_type"];

                if ($childStructType == "file") {
                    // METS:structMap/METS:div/METS:div/METS:fptr - childFptr
                    $structChildFptr = $structCurrentDiv->addChild("METS:fptr");
                    $structChildFptr["FILEID"] = $childHandleId;
                } else {
                    // METS:structMap/METS:div/METS:div/METS:div - childDiv
                    $structChildDiv = $structCurrentDiv->addChild("METS:div");
                    $structChildDiv["TYPE"] = $childStructType;
                    $structChildMptr = $structChildDiv->addChild("METS:mptr");
                    $structChildMptr["LOCTYPE"] = "HANDLE";
                    $structChildMptr["xlink:href"] = "http://hdl.handle.net/".si4config("handlePrefix")."/".$childHandleId;
                }
            }
        }

        // * Behaviour

        // METS:behaviorSec
        $behaviorSecArr = $xmlDoc->xpath("METS:behaviorSec");
        if (count($behaviorSecArr)) $behaviorSec = $behaviorSecArr[0];
        else $behaviorSec = $xmlDoc->addChild("METS:behaviorSec");
        //$behaviorSec["xmlns:xlink"] = "http://www.w3.org/1999/xlink";
        $behaviorSec["ID"] = "si4.behavior";

        // METS:behaviorSec/METS:behavior
        $behaviorArr = $behaviorSec->xpath("METS:behavior");
        if (count($behaviorArr)) $behavior = $behaviorArr[0];
        else $behavior = $behaviorSec->addChild("METS:behavior");
        $behavior["BTYPE"] = $this->struct_subtype;

        // METS:behaviorSec/METS:behavior/METS:mechanism
        $bMechanismArr = $behavior->xpath("METS:mechanism");
        if (count($bMechanismArr)) $bMechanism = $bMechanismArr[0];
        else $bMechanism = $behavior->addChild("METS:mechanism");
        $bMechanism["LOCTYPE"] = "URL";
        //$bMechanism["xlink:href"] = "resources/assets/xsd/default/";
        //$bMechanism->addAttribute("xlink:href", "resources/assets/xsd/default/", "http://www.w3.org/TR/xlink");

        //var_dump($xmlDoc->getDocNamespaces(true));
        //die();


        // Format XML
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlDoc->asXML());

        $this->xml = $dom->saveXML();

        Timer::stop("xmlParsing");
    }

    /*
    private $hierarchy = null;
    public function getHierarchy() {
        if (!$this->hierarchy) {
            $this->hierarchy = EntitySelect::selectEntityHierarchy(["handle_id" => $this->handle_id]);
        }
        return $this->hierarchy;
    }

    private $parentHierarchy = null;
    public function getParentHierarchy() {
        if (!$this->parentHierarchy) {
            $this->parentHierarchy = EntitySelect::selectEntityHierarchy(["handle_id" => $this->parent]);
        }
        return $this->parentHierarchy;
    }
    */


    public function findParentHierarchy() {
        return EntitySelect::selectParentHierarchy($this->parent);
    }
    public function findChildren() {
        return EntitySelect::selectChildren($this->handle_id);
    }




    // Calculates primary entity
    public function calculateParents() {
        Timer::start("calculateParents");
        switch ($this->struct_type) {
            case "collection":
                if ($this->parent) {
                    $this->entity_type = "dependant";
                    //$hierarchy = EntitySelect::selectEntityHierarchy(["handle_id" => $this->parent]);
                    $parentHierarchy = $this->findParentHierarchy();
                    $parents = Si4Util::pathArg($parentHierarchy, "data", []);
                    $this->primary = $parents[0]["handle_id"];
                } else {
                    $this->entity_type = "primary";
                    $this->primary = $this->handle_id;
                }
                break;

            case "file":
                $this->entity_type = "primary";
                $this->primary = $this->handle_id;
                break;

            case "entity": default:
                $this->entity_type = "primary";
                $this->primary = $this->handle_id;
                if ($this->parent) {
                    //$hierarchy = EntitySelect::selectEntityHierarchy(["handle_id" => $this->parent]);
                    $parentHierarchy = $this->findParentHierarchy();
                    //$hierarchy = $this->getParentHierarchy();
                    $parents = Si4Util::pathArg($parentHierarchy, "data", []);
                    $lastCollectionParent = "";
                    // Find first entity parent for primary and take it's parent for collection
                    foreach ($parents as $parent) {
                        if ($parent["struct_type"] == "collection") {
                            $lastCollectionParent = $parent["handle_id"];
                        } else if ($parent["struct_type"] == "entity") {
                            $this->entity_type = "dependant";
                            $this->primary = $parent["handle_id"];
                            break;
                        }
                    }
                    $this->collection = $lastCollectionParent;
                }
                break;
        }
        Timer::stop("calculateParents");
    }

    private static $rootEntity = null;
    public static function getRootEntity() {
        if (!self::$rootEntity) {
            self::$rootEntity = self::query()->where(['handle_id' => env("SI4_ELASTIC_ROOT_COLLECTION")])->get()->first();
        }
        return self::$rootEntity;
    }


    /**
     * @param \App\Models\StructType $structType
     * @param UploadedFile $uploadedFile
     * @return Entity
     */
    // public static function createFromUpload($structType, UploadedFile $uploadedFile) : Entity
    public static function createFromUpload($structType, UploadedFile $uploadedFile)
    {
        $entity = new self;
        $entity->struct_type = $structType;
        $entity->child_order = 0;
        $entity->xml = file_get_contents($uploadedFile->getPathname());
        $entity->save();

        return $entity;
    }

    /**
     * @param string $xmlContent
     * @param array $errors
     * @return bool
     */
    //public static function xmlStringSchemaValidate(string $xmlContent, string $structType, array &$errors = []) : bool
    public static function xmlStringSchemaValidate(string $xmlContent, string $structType, array &$errors = [])
    {
        libxml_clear_errors();
        libxml_use_internal_errors(true);

        /*
        switch ($structType) {
            case "entity": default:
                $schemaFile = "mets_entity.xsd";
                break;
            case "collection":
                $schemaFile = "mets_collection.xsd";
                break;
            case "file":
                $schemaFile = "mets_entity.xsd";
                break;
        }
        */
        $schemaFile = "mets.xsd";

        $xml = new \DOMDocument();
        $xml->loadXML($xmlContent);
        //$valid = $xml->schemaValidate(asset("xsd/mets.xsd"));
        $valid = $xml->schemaValidate(asset("xsd/".$schemaFile));
        if(!$valid){
            $errors = libxml_get_errors();
        }

        return $valid;
    }

    public function save(array $options = array())
    {
        parent::save($options);
        $this->backupXml();
    }

    public function backupXml() {
        $hierarchy = $this->findParentHierarchy()['data'];
        $filePath = env('SI4_BACKUP_DIR');
        
        $parent = $hierarchy;
        foreach($hierarchy as $parent)
            $filePath .= '/' . $parent['handle_id'];

        $filePath .= "/mets.xml";
        $filePath = str_replace("//", "/", $filePath);
        
        // Ensure directory structure
        if(!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        
        file_put_contents($filePath, $this->xml);
    }
}