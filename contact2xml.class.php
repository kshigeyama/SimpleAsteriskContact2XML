<?php
/***
 * contact2xml.class.php
 * Asterisk / FreePBX Phonebook XML create PHP Script for ContactManager
 * 
 * Copyright(C) 2021 kshigeyama.
 * 
 */

class createXMLPhoneBook_gs extends XMLPhoneBook {
    public function fetchData( $FORCE_FLAG = 0 ){
        global $FILENAME, $ACCOUNTIDX;

        $oResult1 = $this->getGroup();
        $oResult2 = $this->getContactBody();

        //create XML Object 
        $root = '<?xml version="1.0" encoding="UTF-8" ?><AddressBook/>';
        $xml = new SimpleXMLElement( $root );
        $xml -> addChild('Version', 1);

        // Group
        $numrows1 = count($oResult1);
        $row = 0;
        for ($row = 0 ; $row < $numrows1 ; $row++) {
            if (!is_null($oResult1[$row][0])) {
                $directoryGroup = $xml -> addChild('pbgroup');
                $directoryGroup -> addChild('name', $oResult1[$row][2]);
                $directoryGroup -> addChild('id', $oResult1[$row][0]);
            }
        }

        // contacts
        $numrows2 = count($oResult2);
        $row = 0;
        for ($row = 0 ; $row < $numrows2 ; $row++) {
            if (!is_null($oResult2[$row][0])) {
                $directoryEntry = $xml -> addChild('Contact');
                $directoryEntry -> addChild('LastName', $oResult2[$row][0]);
                $directoryEntry -> addChild('Group', $oResult2[$row][2]);
                $directoryPhone  = $directoryEntry -> addChild('Phone');
                $directoryPhone -> addAttribute('Type', $this->PhoneTypeConvert($oResult2[$row][3]));
                $directoryPhone -> addChild('phonenumber', $oResult2[$row][1]);
                $accountIndex = $ACCOUNTIDX[ intval($oResult2[$row][2]) ];
                if(is_null($accountIndex)){
                    $accountIndex = 1;
                }
                $directoryPhone -> addChild('accountindex', $accountIndex);

                if( $oResult2[$row][0] == $oResult2[$row+1][0]){
                    $row++;
                    $directoryPhone  = $directoryEntry -> addChild('Phone');
                    $directoryPhone -> addAttribute('Type', $this->PhoneTypeConvert($oResult2[$row][3]));
                    $directoryPhone -> addChild('phonenumber', $oResult2[$row][1]);
                    $accountIndex = $ACCOUNTIDX[$oResult2[$row][2]];
                    if(!is_numeric($accountIndex)){
                        $accountIndex = 1;
                    }
                    $directoryPhone -> addChild('accountindex', 1);
                }

                if( $oResult2[$row][0] == $oResult2[$row+1][0]){
                    $row++;
                    $directoryPhone  = $directoryEntry -> addChild('Phone');
                    $directoryPhone -> addAttribute('Type', $this->PhoneTypeConvert($oResult2[$row][3]));
                    $directoryPhone -> addChild('phonenumber', $oResult2[$row][1]);
                    $accountIndex = $ACCOUNTIDX[$oResult2[$row][2]];
                    if(!is_numeric($accountIndex)){
                        $accountIndex = 1;
                    }
                    $directoryPhone -> addChild('accountindex', $accountIndex);
                }
            }
        }

        // xml to file store
        $fp = fopen($FILENAME, 'wb');
        fwrite($fp, $xml->asXML());
        fclose($fp);

        return ($xml->asXML()); 
    }

    function PhoneTypeConvert($PhoneType){
        switch($PhoneType){
            case "cell":
                return "Cell";
            case "home":
                return "Home";
            case "work":
                return "Work";
            default:
                return "";
        }
   }

} // end of createXMLPhoneBook_gs


class XMLPhoneBook{

    public function cacheCheck( $FORCE_FLAG = 0 ){
        global $FILENAME, $FETCH_TIME ;
        if( $FORCE_FLAG != 1 ){
            if (file_exists($FILENAME)) {
                $fileTimeStamp = filemtime( $FILENAME );
                $nowTiemStamp  = strtotime( 'now' );
                $timeStampDiff = $FETCH_TIME - ($nowTiemStamp - $fileTimeStamp)/60;
                if( $timeStampDiff > 0 ){
                    return true; 
                }else{
                    return false;
                }

            }
        }
    }

    public function getXML( $FORCE_FLAG = 0 ){
        global $FILENAME;
        if( $this->cacheCheck( $FORCE_FLAG ) ){
            $xml = file_get_contents( $FILENAME );
            return ($xml); 
        } else {
            return $this->fetchData();
        }
    }

    Protected function getGroup(){
        global $db;
        $sql = "select * from contactmanager_groups order by name";
        $results = $db->getAll($sql, DB_FETCHMODE_ORDERED);
        return $results;
    }

    Protected function getContactBody(){
        global $db;
        $sql = "
            SELECT
                    contactmanager_group_entries.displayname AS 'name'
                    ,contactmanager_entry_numbers.number AS 'extension' , contactmanager_groups.id as 'gid'
                    , contactmanager_entry_numbers.type as 'teltype'
                FROM contactmanager_groups 
                LEFT JOIN contactmanager_group_entries ON contactmanager_groups.id = contactmanager_group_entries.groupid
                LEFT JOIN contactmanager_entry_numbers ON contactmanager_group_entries.id = contactmanager_entry_numbers.entryid 
                ORDER BY contactmanager_group_entries.displayname
                ";
        $results = $db->getAll($sql, DB_FETCHMODE_ORDERED);
        return $results;
    }
} // end of XMLPhoneBook

?>