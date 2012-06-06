// ---------------------------------------------------------------
//
// ---------------------------------------------------
// copyright 2000 by voxeo corporation. (see LGPL.txt)
// ---------------------------------------------------
//
// v1.0 Coded in PHP 4.0
// v1.0 Coded by Stephen J. Lewis
//
// This application searches a database file for names matching
// a user inputed search pattern.  The letters associated with
// each of the DTMF keys provided the pattern. The DB file depends 
// on the operating system.
//
//      readme.txt                  <---- This file.
//      LGPL.txt                    <---- Opensource license.
//      NamebyDigits.php            <---- Source code file.
//      Database-Linux.inc          <---- This contains all the abstracted database
//                                        routines for linux (Interbase).
//      Database-Win32.inc          <---- This contains all the abstracted database
//                                        routines for Win32 (ODBC).
// 
// Another include file is used as well in case the developer is using HTTP/FTP
// calls that require username and password verification.
//
//      passwords.inc               <---- Abstracted functions for HTTP and FTP
//                                        passwords.
//
//
// It is also associated with the following audio files:
//      mainmessage.wav
//      searchresults.wav
//      makeyourselection.wav
//      nomatches.wav
//      holdmusic.wav
//      youhavetakentoomuchtime.wav
//      usernotavailable.wav
//      invalidnumber.wav
//
// The database is either an Interbase resource or MS Access file
// and contains one table, named namebydigitsdb, with columns called:
//  	'firstname,' 'lastname,' 'phone,' and 'uniqueid.' 
//
// The basic call flow is as follows: User calls into the
// application, user enters search pattern (e.g., 348 if they
// wanted to search for someone with the last name of "Fitzgerald"
// -- that is, any name that starts with three letters falling 
// into the ranges [d-f][g-i][t-v], respectively; the same 348 
// pattern would find someone whose last name was "Fivarsh", 
// "Ditman", "Egullah", etc.), user selects from list of 
// matches (read using text-to-speech), and then NameByDigits 
// calls a phone number stored in the database that is associated
// with that name.  If the call is successful, the two lines are
// conferenced together.
//
// Quick Start Instructions:
//
// 1. Unzip the NameByDigits.zip package onto your server.
//
// 2. Connect the database file as an ODBC data source.
//
// 3. Point a community.voxeo.com phone number to the voice 
//    application.
//
//   a. Point your browser to the voxeo community page:
//	http://community.voxeo.com/. Click on the "account login" 
//	button. 
//
//   b. Click the "log in here" link. (Or create an account if 
//	you do not already have one by clicking the "you can 
//	get one here" link.)
//
//   c. On the "account administration" page, click the "phone 
//	number administration and url mapping" link.
//
//   d. Fill in the url mapping form:
//
//	   select a city near you 
//
//         platform: leave it on the "CallXML" default
//	        url: point to the namebydigits.php file
//
//	Click the "Add Mapping" button.
//
//   e. A new number should appear with the URL. Test the URL link 
//	by clicking it. You should see a XML file that starts with 
//	the following line:
//
//	<?xml version="1.0" encoding="UTF-8" ?> 
//
// ---------------------------------------------------------------
