<?php

namespace App\Library\Services\Ipsp;

class XmlData extends \SimpleXMLElement
{
    /**
     * @param array $array
     */
    public function arrayToXml(array $array = [])
    {
        foreach($array as $key=>$val) {
            if(is_numeric($key)) continue;
            if( is_array($val) ) {
                $this->addChild($key);
                $this->arrayToXml($val);
            } else {
                $this->addChild($key,$val);
            }
        }
    }

    /**
     * @return array
     */
    public function xmlToArray(): array
    {
        $result = [];
        $children = $this->children();
        foreach($children as $item){
            if($item->count()>0)
                $result[$item->getName()] = $item->xmlToArray();
            else
                $result[$item->getName()] = (string)$item;
        }
        
        return $result;
    }
}
