<?php
/***
 * contact2xml.conf
 * Asterisk / FreePBX Phonebook XML create PHP Script for ContactManager
 * 
 * Copyright(C) 2021 kshigeyama.
 * 
 */

/* configration start  */
// URL Scheme
define( "URL_SHCEME" , 'http://' );


// minite
static $FETCH_TIME = 30;

// XML Store filename.
static $FILENAME = 'phonebook.xml';

//MODE1 
// 0 = display Name
// 1 = Surname,LastName
define( "MODE1" , 0 );


//
// Default AccoutIDX set SIP-LINE No.
//
$DEFAULT_ACCOUNTIDX = 1;

// Account Index SIP-LINE
// $ACCOUNTIDX[**GroupID**] = SIP-LINE NUMBER;
// $ACCOUNTIDX[5] = 2;

/* configration end  */
?>