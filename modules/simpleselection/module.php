<?php

//
// Copyright (C) 1999-2005 Vision with Technology, All rights reserved.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@visionwt.com if any conditions of this licencing isn't clear to
// you.
//
// Author:       Paul Forsyth
// Version:      $Id: module.php 174 2007-02-26 19:19:41Z paulf $


$Module = array( "name" => "bookings",
                 "variable_params" => true );

$ViewList = array();

$ViewList["request"] = array(
    "functions" => array( 'list' ),
    "script" => "request.php",
    "ui_context" => "administration",
    "default_navigation_part" => 'bookingnavigationpart',
    "params" => array(  ) );

$ViewList["list"] = array(
    "functions" => array( 'list' ),
    "script" => "list.php",
    "ui_context" => "administration",
    "default_navigation_part" => 'bookingnavigationpart',
    "params" => array(  ) );

$ViewList["export"] = array(
    "functions" => array( 'export' ),
    "script" => "export.php",
    "ui_context" => "administration",
    "default_navigation_part" => 'bookingnavigationpart',
    "params" => array(  ) );

$FunctionList['request'] = array( );
$FunctionList['list'] = array( );
$FunctionList['export'] = array( );

?>
