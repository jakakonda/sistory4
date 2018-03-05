<?php

namespace App\Models;

use App\Helpers\EntitySelect;
use App\Helpers\Enums;
use App\Helpers\Si4Util;
use App\Xsd\AnySimpleTypeHandler;
use App\Xsd\AnyTypeHandler;
use App\Xsd\XmlDataATypeHandler;
use App\Xsd\DcTypeHandler;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\HandlerRegistryInterface;

use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\BaseTypesHandler;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\XmlSchemaDateHandler;
use Psr\Log\Test\LoggerInterfaceTest;


/**
 * App\Models\Entity
 *
 * @property int $id
 * @property int $struct_type_id
 * @property string $data
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
        'parent',
        'primary',
        'name',
        'struct_type',
        'entity_type',
        'data',
        'active'
    ];

    /**
     * @return bool
     */
    // public function dataSchemaValidate() : bool
    public function dataSchemaValidate()
    {
        return self::xmlStringSchemaValidate($this->data, $this->struct_type);
    }

    public function dataToObject()
    {
        if (!$this->data) return null;
        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->addMetadataDir(app_path("Xsd/Mets"), 'App\Xsd\Mets');
        $serializerBuilder->configureHandlers(function (HandlerRegistryInterface $handler) use ($serializerBuilder) {
            $serializerBuilder->addDefaultHandlers();
            $handler->registerSubscribingHandler(new BaseTypesHandler()); // XMLSchema List handling
            $handler->registerSubscribingHandler(new XmlSchemaDateHandler()); // XMLSchema date handling

            //$handler->registerSubscribingHandler(new XmlDataATypeHandler());
            // $handler->registerSubscribingHandler(new YourhandlerHere());
            //$handler->registerSubscribingHandler(new AnySimpleTypeHandler());
            $handler->registerSubscribingHandler(new DcTypeHandler());
            $handler->registerSubscribingHandler(new AnyTypeHandler());

        });

        $serializer = $serializerBuilder->build();

        // deserialize the XML into object
        $object = $serializer->deserialize($this->data, 'App\Xsd\Mets\Mets', 'xml');
        $array = $object->toArray();


        return $array;
    }

    // Calculates primary entity
    public function calculatePrimary() {

        switch ($this->struct_type) {
            case "collection":
                if ($this->parent) {
                    $this->entity_type = "dependant";
                    $hierarchy = EntitySelect::selectEntityHierarchy(["handle_id" => $this->parent]);
                    $parents = Si4Util::pathArg($hierarchy, "data/parents", []);
                    $parents[] = Si4Util::pathArg($hierarchy, "data/currentEntity", []);
                    $this->primary = $parents[0]["handle_id"];
                } else {
                    $this->entity_type = "primary";
                    $this->primary = "";
                }
                break;

            case "file":
                $this->entity_type = "primary";
                $this->primary = "";
                break;

            case "entity": default:
                $this->entity_type = "primary";
                $this->primary = "";
                if ($this->parent) {
                    $hierarchy = EntitySelect::selectEntityHierarchy(["handle_id" => $this->parent]);
                    $parents = Si4Util::pathArg($hierarchy, "data/parents", []);
                    $parents[] = Si4Util::pathArg($hierarchy, "data/currentEntity", []);
                    // Find first entity parent
                    foreach ($parents as $parent) {
                        if ($parent["struct_type"] == "entity") {
                            $this->entity_type = "dependant";
                            $this->primary = $parent["handle_id"];
                            break;
                        }
                    }
                }
                break;
        }
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
        $entity->data = file_get_contents($uploadedFile->getPathname());
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
}