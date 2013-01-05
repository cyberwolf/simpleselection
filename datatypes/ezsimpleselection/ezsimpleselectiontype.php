<?php

/*
    Simple Selection extension for eZ publish 3.x
    Copyright (C) 2005 Vision With Technology

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

/*!
  \class   ezsimpleselectiontype ezsimpleselectiontype.php
  \ingroup eZDatatype
  \brief   Handles the single selections from a string.
  \author  Vittal Aithal <vittal@visionwt.com>
  \version 0.1

  This datatype allows a class author to define a selection
  with a single, text delimited field.

  The delimiter used is '~|~', so, a field value of:
    foo~|~bar
  results in a selection list, with the options of 'foo' and 'bar'

  It is also possible to use number ranges for option values. e.g.
  [3:6]
  will produce a set of options 3, 4, 5 and 6.

  It is also possible to define specific name/value pairs. e.g.
  [foo=>bar]
  will produce a single option <option value="bar">foo</option>

  The above types can all be mixed together, such that things like
  this are valid:
  option one~|~option 2~|~[4:10]~|~[null=>]~|~last option

*/

class eZSimpleSelectionType extends eZDataType {

    const DATATYPESTRING = 'ezsimpleselection';
    const MULTI_FIELD = 'data_int1';
    const OPTS_FIELD = 'data_text5';
    const OPTS_VALUES_FIELD = 'data_text4';
    const CHECKBOX_FIELD = 'data_text3';
    const DELIMITER_FIELD = 'data_text2';

    /*!
     Constructor
    */
    function eZSimpleSelectionType() {
        $this->eZDataType(self::DATATYPESTRING,
                            ezi18n('extension/datatypes', "Simple Selection",
                                    'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'text' )));
    }

    /*!
     Sets the default object value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $optFields = explode("~|~",$contentClassAttribute->attribute( self::OPTS_FIELD ));
            $optDefaults = explode("~|~",$contentClassAttribute->attribute( self::OPTS_VALUES_FIELD ));

            $defaultArray = array();

            if (count($optDefaults) > 0)
            {
                $count=0;
                foreach ($optDefaults as $default)
                {
                    if (strcasecmp($default, "yes")==0)
                    {
                        $defaultArray[] = $optFields[$count];
                    }

                    $count++;
                }
            }

            if ( count($defaultArray) > 0 )
            {
                $contentObjectAttribute->setAttribute( "data_text", implode('***', $defaultArray) );
            }
        }
    }



    /*!
     Validate the class input.
    */
    function validateClassAttributeHTTPInput($http,
                                             $base,
                                             $classAttribute)
    {
        $clsid = $classAttribute->attribute('id');
        $prefix = "${base}_" . self::DATATYPESTRING;

        $optsField = "${prefix}_options_${clsid}";
        $optsDefaultValuesField = "${prefix}_option_values_${clsid}";

        // Perform some tests on the default values
        if ($http->hasPostVariable($optsField) && $http->hasPostVariable($optsDefaultValuesField))
        {
            $options = $http->postVariable($optsField);
            $optionDefaultValues = $http->postVariable($optsDefaultValuesField);

            if ( count(explode("~|~", $optionDefaultValues)) > count(explode("~|~", $options)) )
            {
                return eZInputValidator::STATE_INVALID;
            }
        }

        return eZInputValidator::STATE_ACCEPTED;
    }


    /*!
     Get the values sent in when the class is stored.
    */
    function fetchClassAttributeHTTPInput($http,
                                          $base,
                                          $classAttribute)
    {
        $clsid = $classAttribute->attribute('id');
        $prefix = "${base}_" . self::DATATYPESTRING;
        $optsField = "${prefix}_options_${clsid}";
        $optsValueField = "${prefix}_option_values_${clsid}";

        if ($http->hasPostVariable($optsField))
        {
            $multiField = "${prefix}_ismultiple_value_${clsid}";
            $delimiterField = "${prefix}_delimiter_${clsid}";
            $checkboxField = "${prefix}_checkbox_${clsid}";

            // Deal with the multiple selection flag
            $isMultipleSelection = false;
            if ($http->hasPostVariable($multiField))
            {
                $isMultipleSelection = true;
            }
            $classAttribute->setAttribute(self::MULTI_FIELD,
                                            ($isMultipleSelection ? 1 : 0));

            // Deal with the delimiter string
            $delimiter = false;
            if ($http->hasPostVariable($delimiterField))
            {
                $delimiter = $http->postVariable($delimiterField);
            }
            $classAttribute->setAttribute(self::DELIMITER_FIELD,
                                            $delimiter);

            // Deal with the checkbox style
            $useCheckbox = false;
            if ($http->hasPostVariable($checkboxField))
            {
                $useCheckbox = true;
            }
            $classAttribute->setAttribute(self::CHECKBOX_FIELD,
                                            $useCheckbox);

            // Deal with our options string. No need to test for existance again.
            $classAttribute->setAttribute(self::OPTS_FIELD,
                                          $http->postVariable($optsField));

            // Deal with the default option values
            $classAttribute->setAttribute(self::OPTS_VALUES_FIELD,
                                          $http->postVariable($optsValueField));


        }

        return true;
    }


    /*!
     Make sure the data we're sent back in the form is correct.
    */
    function validateObjectAttributeHTTPInput($http, $base,
                                                $contentObjectAttribute) {
        $prefix = "${base}_" . self::DATATYPESTRING;
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $selectionField = "${prefix}_selected_array_" .
                            $contentObjectAttribute->attribute('id');
        $isRequired = ($classAttribute->attribute('is_required') == 1);
        $isMultipleSelection = ($classAttribute->attribute(self::MULTI_FIELD) == 1);

        // See what to do if we've been sent the field
        if ($http->hasPostVariable($selectionField)) {
            $selectOptions = $http->postVariable($selectionField);

            // Make sure we've selected something - we might have
            // selected multiple empty values!
            if ($isRequired) {
                foreach ($selectOptions as $selVal) {
                    if (!empty($selVal)) {
                        // Great! We've got a real value
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                }
                // Doh! All the things selected were empty!
                return eZInputValidator::STATE_INVALID;
            }
            // We're easy going, anything is acceptable.
            return eZInputValidator::STATE_ACCEPTED;
        } else {
            if ($isMultipleSelection && $isRequired) {
                $contentObjectAttribute->setValidationError(
                    'This is a required field. ' .
                    'You need to select at least one option.');
            } elseif ($isRequired) {
                $contentObjectAttribute->setValidationError(
                    'No POST variable. Please check your configuration.');
            } else {
                return eZInputValidator::STATE_ACCEPTED;
            }
            return eZInputValidator::STATE_INVALID;
        }
    }


    /*
     Make sure the input is valid when sent in a collection.
    */
    function validateCollectionAttributeHTTPInput($http, $base,
                                                   $contentObjectAttribute) {
        return $this->validateObjectAttributeHTTPInput($http, $base,
                                                    $contentObjectAttribute);
    }


    /*!
    */
    function fetchObjectAttributeHTTPInput($http, $base,
                                            $contentObjectAttribute) {
        $prefix = "${base}_" . self::DATATYPESTRING;
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $selectionField = "${prefix}_selected_array_" .
                            $contentObjectAttribute->attribute('id');
        $isMultipleSelection = ($classAttribute->attribute(self::MULTI_FIELD) == 1);

        if ($http->hasPostVariable($selectionField)) {
            $selectOptions = $http->postVariable($selectionField);

            $idString = implode('***', $selectOptions);

            $contentObjectAttribute->setAttribute('data_text', $idString);
            return true;
        } else {
            if ($isMultipleSelection) {
                $contentObjectAttribute->setAttribute('data_text', '');
                return true;
            }
        }
        return false;
    }


    /*!
     Returns the selected options by id.
    */
    function objectAttributeContent($contentObjectAttribute) {
        $idString = $contentObjectAttribute->attribute('data_text');
        return explode( '***', $idString );
    }


    /*!
     Returns the content data for the given content class attribute.
    */
    function classAttributeContent($classAttribute)
    {
        $optionArray = array();
        $optValues = $this->makeValueArray($classAttribute->attribute(
                                        self::OPTS_FIELD));

        $optDefaultValues = explode("~|~",$classAttribute->attribute(self::OPTS_VALUES_FIELD));

        if (count($optValues) > 0)
        {
            $count = 0;
            foreach (array_keys($optValues) as $key)
            {
                $defaultValue = "No";
                if (isset($optDefaultValues[$count]))
                {
                    $defaultValue = $optDefaultValues[$count];
                }

                $optionArray[] = array(	'name' => $optValues[$key],
                                        'identifier' => $key,
                                        'default' => $defaultValue);
                $count++;
            }
        }
        else
        {
            $optionArray[] = array( 'name' => '',
                                    'identifier' => '');
        }
        return array(
                'options' => $optionArray,
                'is_multiselect' => $classAttribute->attribute(
                                            self::MULTI_FIELD),
                'delimiter' => $classAttribute->attribute(
                                            self::DELIMITER_FIELD),
                'checkbox' => $classAttribute->attribute(
                                            self::CHECKBOX_FIELD),
                'option_string' => $classAttribute->attribute(
                                            self::OPTS_FIELD),
                'option_string_values' => $classAttribute->attribute(
                                            self::OPTS_VALUES_FIELD),
                );
    }


    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput($collection,
                                                $collectionAttribute,
                                                $http, $base,
                                                $contentObjectAttribute) {
        $prefix = "${base}_" . self::DATATYPESTRING;
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $selectionField = "${prefix}_selected_array_" .
                            $contentObjectAttribute->attribute('id');
        $optValues = $this->makeValueArray($classAttribute->attribute(
                                        self::OPTS_FIELD));
        $resultArray = array();

        if ($http->hasPostVariable($selectionField)) {
            $selectOptions = $http->postVariable($selectionField);
            foreach ($selectOptions as $selVal) {
                if (array_key_exists($selVal, $optValues)) {
                    $resultArray[] = $optValues[$selVal];
                } else {
                    $resultArray[] = $selVal;
                }
            }

            $delimiter = $classAttribute->attribute(
                                    self::DELIMITER_FIELD);
            if (empty($delimiter)) {
                $delimiter = ', ';
            }
            $dataText = implode($delimiter, $resultArray);
            $collectionAttribute->setAttribute('data_text', $dataText);
        } else {
            $collectionAttribute->setAttribute('data_text', '');
        }
        return true;
    }


    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData($contentObjectAttribute) {
        $content = $this->objectAttributeContent($contentObjectAttribute);

        return array(array(	'id' => '',
                            'text' => implode(' ', $content),
                            'literal' => false));
    }


    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable() {
        return true;
    }

    /*!
     Returns the text.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /*!
     \return true if the datatype can be be used as an information collector
    */
    function isInformationCollector() {
        return true;
    }


    /*!
     Take a value string, split it up, and return the resulting
     array. We parse any internal syntax in the array too, which
     currently means expanding number ranges.

     \return An array containing name/value pairs (value is the key)
    */
    function makeValueArray($values) {
        $result = array();
        foreach (explode("~|~", $values) as $val) {
            // Make sure we handle range values
            if (preg_match('/^\[(\d+):(\d+)\]$/', $val, $matches)) {
                if ($matches[1] < $matches[2]) {
                    for ($i = $matches[1]; $i <= $matches[2]; $i++) {
                        $result[$i] = $i;
                    }
                } else {
                    for ($i = $matches[1]; $i >= $matches[2]; $i--) {
                        $result[$i] = $i;
                    }
                }
            } elseif (preg_match('/^\[(.+?)=>(.*?)\]$/', $val, $matches)) {
                // Handle name=>value pairs... we can allow a null value
                $result[$matches[2]] = $matches[1];
            } else {
                $result[$val] = $val;
            }
        }
        return $result;
    }


    /*!
     \reimp
    */
    function sortKey( $contentObjectAttribute )
    {
        $trans = eZCharTransform::instance();
        return $trans->transformByGroup( $contentObjectAttribute->attribute( 'data_text' ), 'lowercase' );
    }

    /*!
     \reimp
    */
    function sortKeyType()
    {
        return 'string';
    }

}

eZDataType::register(eZSimpleSelectionType::DATATYPESTRING,
                        "eZSimpleSelectionType" );
?>
