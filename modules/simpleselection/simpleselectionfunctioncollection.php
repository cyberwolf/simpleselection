<?php
class SimpleSelectionFunctionCollection
{
    function fetchRelatedSelections($contentclassattribute_id, $selection)
    {
        $db = eZDB::instance();
        $query = "SELECT DISTINCT ezcontentobject_attribute.contentobject_id,ezcontentobject_attribute.version FROM ezcontentobject, ezcontentobject_attribute
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

    function fetchList( $classAttributeID, $nodeID, $subtreeParams = array() ) {
        $classAttribute = eZContentClassAttribute::fetch( $classAttributeID );
        $classAttributeContent = $classAttribute->attribute( 'content' );
        $classAttributeIdentifier = $classAttribute->attribute( 'identifier' );
        $list = array();

        foreach ( $classAttributeContent['options'] as $option )
        {
            $list[$option['identifier']] = array(
                'name' => $option['name'],
                'collection' => array(),
            );
        }

        $subtreeParams['ClassFilterType'] = 'include';
        $subtreeParams['ClassFilterArray'] = array( $classAttribute->attribute('contentclass_id') );

        $nodes = eZContentObjectTreeNode::subTreeByNodeID($subtreeParams, $nodeID);
		if(!empty($nodes))
	    {
	        foreach ( array_keys( $nodes ) as $key )
	        {
	            $node = $nodes[$key];
	            $dataMap = $node->attribute( 'data_map' );
	            $attribute = $dataMap[$classAttributeIdentifier];

	            foreach ( $attribute->attribute( 'content' ) as $selectedOption )
	            {
	                $list[$selectedOption]['collection'][] = $node;
	            }
	        }
		}
        return array( 'result' => $list );
    }
}
