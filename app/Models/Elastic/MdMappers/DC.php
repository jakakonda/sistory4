<?php

namespace App\Models\Elastic\MdMappers;

use App\Helpers\DcHelpers;

class DC extends AbstractMdMapper
{
    function mdTypeToHandle() {
        return "DC";
    }

    function mapXmlData($xmlData) {
        $result = [];
        $fieldDefs = DcHelpers::$si4FieldDefinitions;

        foreach ($xmlData as $key => $val) {

            // Continue if empty field value
            if (!$val || !count($val)) continue;

            // Find si4 field by DC element
            $fieldDef = null;
            switch ($key) {
                case "TitlePropName":
                    $fieldDef = $fieldDefs["title"];
                    break;
                case "CreatorPropName":
                    $fieldDef = $fieldDefs["creator"];
                    break;
                case "DatePropName":
                    $fieldDef = $fieldDefs["date"];
                    break;
                case "DescriptionPropName":
                    $fieldDef = $fieldDefs["description"];
                    break;
            }

            // Continue if unknown si4 field
            if (!$fieldDef) continue;

            // Prepare an empty array for the field data if not exists yet
            if (!isset($result[$fieldDef["fieldName"]])) $result[$fieldDef["fieldName"]] = [];

            foreach ($val as $valIdx => $valValue) {
                $value = $valValue["value"];
                $lang = isset($valValue["LangPropName"]) ? $valValue["LangPropName"] : "";
                if (!$lang) $lang = DcHelpers::$defaultLang;

                if ($fieldDef["hasLanguage"]) {
                    $result[$fieldDef["fieldName"]][] = [ "lang" => $lang, "value" => $value ];
                } else {
                    $result[$fieldDef["fieldName"]][] = [ "value" => $value ];
                }
            }
        }

        return $result;
    }
}