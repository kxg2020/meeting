<?php
namespace app\index\service;

class Tool{
    use Singleton;

    /*
     * jsonDecode
     */
    public function jsonDecode($data){
        return json_decode($data,1);
    }

    /*
     * jsonEncode
     */
    public function jsonEncode($data){
        return json_encode($data,256);
    }

    /*
     * xml转array
     */
    public function xmlToArray($xml){
        if (file_exists($xml)) {
            libxml_disable_entity_loader(false);
            $xml_string = simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            libxml_disable_entity_loader(true);
            $xml_string = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        $result = json_decode(json_encode($xml_string), true);
        return $result;
    }

    /*
     * array转xml
     */
    public function arrayToXml($arr,$dom=null,$node=null,$root='xml',$cdata=false){
	if(!$dom){
		$dom = new \DOMDocument('1.0','utf-8');
	}
    if(!$node){
        $node = $dom->createElement($root);
        $dom->appendChild($node);
    }
    foreach ($arr as $key=>$value){
        $child_node = $dom->createElement(is_string($key) ? $key : 'node');
        $node->appendChild($child_node);
        if (!is_array($value)){
            if (!$cdata) {
                $data = $dom->createTextNode($value);
            }else{
                $data = $dom->createCDATASection($value);
            }
            $child_node->appendChild($data);
        }else {
           $this->arrayToXml($value,$dom,$child_node,$root,$cdata);
        }
    }
    return $dom->saveXML();
    }

}