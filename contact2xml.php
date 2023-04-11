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

$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
    include_once('/etc/asterisk/freepbx.conf');
}

// force fetch flags.
$FORCE_FLAG = array_key_exists('FORCE', $_GET) ? $_GET['FORCE'] : NULL ;
if( !is_numeric($FORCE_FLAG) ){
    $FORCE_FLAG = 0;
}

// IP-Phone brand Type
$BRAND_TYPE = array_key_exists('TYPE', $_GET) ? $_GET['TYPE'] : NULL ;

// MODE1  none : display Name, 1 = Surname,LastName
$MODE1 = array_key_exists('MODE1', $_GET) ? $_GET['MODE1'] : NULL ;
if( !is_numeric($MODE1) ){
    $MODE1 = MODE1;
}



switch($BRAND_TYPE){
    case "gs": // GRANDSTREAM
        //$sXML = createXMLPhoneBook_gs( $FETCH_TIME, $FORCE_FLAG , $FILENAME );
        $oXMLContact = new createXMLPhoneBook_gs();
        $sXML = $oXMLContact->getXML($FORCE_FLAG, $MODE1);
        break;
    case "gs3000": // GRANDSTREAM
        foreach($ACCOUNTIDX as $i => $v) {
            $ACCOUNTIDX[$i] = $v - 1;
        }
        $DEFAULT_ACCOUNTIDX = $DEFAULT_ACCOUNTIDX  - 1;
        $oXMLContact = new createXMLPhoneBook_gs();
        $sXML = $oXMLContact->getXML($FORCE_FLAG, $MODE1);
        break;
    default:
        $oXMLContact = new createXMLPhoneBook_gs();
        $sXML = $oXMLContact->getXML($FORCE_FLAG, $MODE1);
        break;
}

header ("content-type: text/xml");
echo $sXML;

exit;

?>