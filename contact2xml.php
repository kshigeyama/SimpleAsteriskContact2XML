<?php
/***
 * contact2xml.php
 * Asterisk / FreePBX Phonebook XML create PHP Script for ContactManager
 * 
 * Copyright(C) 2021 kshigeyama.
 * 
 */
require_once("contact2xml.conf.php");
require_once("contact2xml.class.php");

$url = URL_SHCEME.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
    include_once('/etc/asterisk/freepbx.conf');
}

// force fetch flags.
$FORCE_FLAG1 = array_key_exists('FORCE', $_GET) ? $_GET['FORCE'] : NULL ;
$FORCE_FLAG2 = array_key_exists('force', $_GET) ? $_GET['force'] : NULL ;

if ( is_numeric($FORCE_FLAG1) ){
    $FORCE_FLAG = $FORCE_FLAG1;
} elseif ( is_numeric($FORCE_FLAG2) ){
    $FORCE_FLAG = $FORCE_FLAG2;
} else {
    $FORCE_FLAG = 0;
}

// IP-Phone brand Type
if ( array_key_exists('BTYPE', $_GET) ){
    $BRAND_TYPE = $_GET['BTYPE'];
} elseif ( array_key_exists('btype', $_GET) ){
    $BRAND_TYPE = $_GET['btype'];
} elseif ( array_key_exists('TYPE', $_GET) ){
    $BRAND_TYPE = $_GET['TYPE'];
}  elseif ( array_key_exists('type', $_GET) ){
    $BRAND_TYPE = $_GET['type'];
} else {
    $BRAND_TYPE = null;
}

// MODE1  none : display Name, 1 = Surname,LastName
$MODE1 = array_key_exists('MODE1', $_GET) ? $_GET['MODE1'] : NULL ;
if( !is_numeric($MODE1) ){
    $MODE1 = MODE1;
}

// GPID  get No of Group ID
$iGPID = array_key_exists('GPID', $_GET) ? $_GET['GPID'] : NULL ;

// ACID  get No of Account ID / SIP-LineID bundle
$ACID = array_key_exists('ACID', $_GET) ? $_GET['ACID'] : NULL ;
if( is_array($ACID ) ){
    $ACCOUNTIDX = $ACID;
}


$iSeekKey = 0;

switch($BRAND_TYPE){
    case "gs": // GRANDSTREAM
    case "gs1000":
        $oXMLContact = new createXMLPhoneBook_gs();
        $sXML = $oXMLContact->getXML( $FORCE_FLAG, $MODE1, $iSeekKey, $iGPID );
        break;
    case "gxv3370": // for GRANDSTREAM Videofone GXV3370
    case "gs3000": // GRANDSTREAM
        $iSeekKey = $iSeekKey - 1;
        $oXMLContact = new createXMLPhoneBook_gs();
        $sXML = $oXMLContact->getXML( $FORCE_FLAG, $MODE1, $iSeekKey, $iGPID );
        break;
    default:
        $oXMLContact = new createXMLPhoneBook_gs();
        $sXML = $oXMLContact->getXML( $FORCE_FLAG, $MODE1, $iSeekKey, $iGPID );
        break;
}

header ("content-type: text/xml");
echo $sXML;

exit;
?>