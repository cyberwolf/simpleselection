<?php
class SimpleSelectionFunctionCollection
{
    function fetchRelatedSelections($contentclassattribute_id, $selection)
    {
        $db =& eZDB::instance();
        $query = " SELECT DISTINCT ezcontentobject_attribute.contentobject_id,ezcontentobject_attribute.version FROM ezcontentobject, ezcontentobject_attribute
WHERE ezcontentobject_attribute.data_text IN ( '$selection' ) AND
ezcontentobject_attribute.contentclassattribute_id = $contentclassattribute_id AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id AND ezcontentobject.current_version = ezcontentobject_attribute.version AND ezcontentobject.status=1";
 
        $array = $db->arrayQuery( $query );
 
        $objects = array();
        foreach ($array as $object)
        {
            $content_object = eZContentObject::fetch($object['contentobject_id']);
            $objects[] = $content_object;
            
        }
 
        return array( 'result' => $objects );
    }
 
}
?>
