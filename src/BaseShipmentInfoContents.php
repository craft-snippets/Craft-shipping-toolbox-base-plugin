<?php

namespace craftsnippets\baseshippingplugin;
use craftsnippets\baseshippingplugin\ShipmentInfoContentsInterface;
use craft\base\Model;

abstract class BaseShipmentInfoContents extends Model implements ShipmentInfoContentsInterface
{
    public $jsonData;

    public function init(): void
    {
        // decode from field value only if json was provided
        if(is_null($this->jsonData)){
            return;
        }
        $data = json_decode($this->jsonData, true);

        // assign json properties
        foreach ($this->getJsonProperties() as $single) {
            $property = $single['value'];
            if(isset($data[$property])){
                $this->{$property} = $data[$property];
            }
        }
    }

    public function getSavedProperty($property)
    {
        $value = $this->{$property} ?? null;
        if(is_null($value)){
            return null;
        }
        return $value;
    }

    public function outputSavedData()
    {
        $properties = [];
        foreach ($this->getJsonProperties() as $single){
            $property = $single['value'];
            $propertyLabel = $single['label'];
            $value = $this->getSavedProperty($property);
            if(empty($value)){
                continue;
            }
            $properties[] = [
                'label' => $propertyLabel,
                'value' => $value,
                'key' => $property,
            ];
        }
        return $properties;
    }

    public function encodeData()
    {
        $array = [
        ];
        foreach ($this->getJsonProperties() as $single) {
            $property = $single['value'];
            $array[$property] = $this->{$property};
        }
        return json_encode($array);
    }

}