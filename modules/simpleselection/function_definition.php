<?php
//
// Created on: <05-Dec-2005 15:11:38 oms>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file function_definition.php
*/

$extension = 'simpleselection';
$base = eZExtension::baseDirectory();
$base_dir = "$base/$extension/modules/$extension/";

$FunctionList = array();

$FunctionList['related_selection'] = array( 'name' => 'related_selection',
                                                'call_method' => array( 'include_file' => $base_dir . 'simpleselectionfunctioncollection.php',
                                                                        'class' => 'SimpleSelectionFunctionCollection',
                                                                        'method' => 'fetchRelatedSelections' ),
                                                'parameter_type' => 'standard',
                                                'parameters' => array( array( 'name' => 'contentclass_id',
                                                                              'type' => 'integer',
                                                                              'required' => true),
                                                                       array( 'name' => 'selection',
                                                                              'type' => 'integer',
                                                                              'required' => false)));

$FunctionList['list'] = array(
    'name' => 'list',
    'call_method' => array( 'include_file' => $base_dir . 'simpleselectionfunctioncollection.php',
                            'class' => 'SimpleSelectionFunctionCollection',
                            'method' => 'fetchList' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'contentclassattribute_id',
                                  'type' => 'integer',
                                  'required' => true),
                           array( 'name' => 'node_id',
                                  'type' => 'integer',
                                  'required' => true),
                           array( 'name' => 'subtree_params',
                                  'type' => 'array',
                                  'required' => false,
                                  'default' => array())));

?>
