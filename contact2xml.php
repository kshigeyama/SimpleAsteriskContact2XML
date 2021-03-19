<?php
/***
 * contact2xml.php
 * Asterisk / FreePBX Phonebook XML create PHP Script for ContactManager
 * 
 * Copyright(C) 2021 kshigeyama.
 * 
 */

/* configration start  */
// minite
$FETCH_TIME = 60;

// XML Store filename.
static $FILENAME = "phonebook.xml";
/* configration end  */


// force fetch flags.
$FORCE_FLAG = array_key_exists('FORCE', $_GET) ? $_GET['FORCE'] : NULL ;
if( !is_numeric($FORCE_FLAG) ){
    $FORCE_FLAG = 0;
}

$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if (!@include_once(getenv('FREEPBX_CONF') ? getenv('FREEPBX_CONF') : '/etc/freepbx.conf')) {
    include_once('/etc/asterisk/freepbx.conf');
}

header ("content-type: text/xml");
echo createXMLPhoneBook_gs( $FETCH_TIME, $FORCE_FLAG , $FILENAME );

exit;

function createXMLPhoneBook_gs( $FETCH_TIME, $FORCE_FLAG , $FILENAME ) {

    // file cache 
    if( $FORCE_FLAG != 1 ){
        if (file_exists($FILENAME)) {
            $fileTimeStamp = filemtime( $FILENAME );
            $nowTiemStamp  = strtotime( 'now' );
            $timeStampDiff = $FETCH_TIME - ($nowTiemStamp - $fileTimeStamp)/60;
            if( $timeStampDiff > 0 ){
                $xml = file_get_contents( $FILENAME );
                return ($xml); 
            }
        }
    }

    global $db;

    $sql1 = "select * from contactmanager_groups order by name";
    $sql2 = "
        SELECT
        CONCAT(contactmanager_group_entries.displayname, ' (', contactmanager_entry_numbers.type,')') AS 'name'
            ,contactmanager_entry_numbers.number AS 'extension' , contactmanager_groups.id as 'gid'
        FROM contactmanager_groups 
        LEFT JOIN contactmanager_group_entries ON contactmanager_groups.id = contactmanager_group_entries.groupid
        LEFT JOIN contactmanager_entry_numbers ON contactmanager_group_entries.id = contactmanager_entry_numbers.entryid 
        ORDER BY contactmanager_group_entries.displayname
    ";
 

    //create XML Object 
    $root = '<?xml version="1.0" encoding="UTF-8" ?><AddressBook/>';
    $xml = new SimpleXMLElement( $root );
    $xml -> addChild('Version', 1);


    // Group
    $results1 = $db->getAll($sql1, DB_FETCHMODE_ORDERED);
    $numrows1 = count($results1);
    $row = 0;
    for ($row = 0 ; $row < $numrows1 ; $row++) {
        if (!is_null($results1[$row][0])) {
            $directoryGroup = $xml -> addChild('pbgroup');
            $directoryGroup -> addChild('name', $results1[$row][2]);
            $directoryGroup -> addChild('id', $results1[$row][0]);
        }
    }

    // contacts
    $results2 = $db->getAll($sql2, DB_FETCHMODE_ORDERED);
    $numrows2 = count($results2);
    $row = 0;
    for ($row = 0 ; $row < $numrows2 ; $row++) {
        if (!is_null($results2[$row][0])) {
            $directoryEntry = $xml -> addChild('Contact');
            $directoryEntry -> addChild('LastName', $results2[$row][0]);
            $directoryEntry -> addChild('Group', $results2[$row][2]);
            $directoryPhone  = $directoryEntry -> addChild('Phone');
            $directoryPhone -> addChild('phonenumber', $results2[$row][1]);
            $directoryPhone -> addChild('accountindex', 1);
        }
    }

    // xml to file strage
    $fp = fopen('phonebook.xml', 'wb');
    fwrite($fp, $xml->asXML());
    fclose($fp);

    return ($xml->asXML()); 
}
?>