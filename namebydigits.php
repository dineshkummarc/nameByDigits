<?PHP

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
//      Database-Linux.inc          <---- This contains all the abstracted database
//                                        routines for linux (Interbase).
//      Database-Win32.inc          <---- This contains all the abstracted database
//                                        routines for Win32 (ODBC).
//      passwords.inc               <---- Abstracted functions for HTTP and FTP
//                                        passwords.
//
//
// It is also associated with the following audio files:
// 		mainmessage.wav
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
// ---------------------------------------------------------------


//-----------------------------------------------------------------------------------------

function Initialize() {
  global $HTTP_GET_VARS;
  
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; 
  echo "<callxml>";
  
  //------------------------------------------------------------------------
  // This annoying little variable is simply incremented and passed along to
  // avoid the AGS's caching.  This helps to ensure that a different URL
  // query string will be passed.
  //------------------------------------------------------------------------

  if ($HTTP_GET_VARS["cachekiller"] == "") {
    echo "<assign var=\"cachekiller\" value=\"1\"/>";
  }  // if statement
  else {
    $HTTP_GET_VARS["cachekiller"]++;
	echo "<assign var=\"cachekiller\" value=\"" . $HTTP_GET_VARS["cachekiller"] . "\"/>";
  }  // else statement
  
}  // initialize function

//----------------------------------------------------------------------------------------

function Closure() {

  echo "</callxml>";

}  // closure function

//----------------------------------------------------------------------------------------

function MainMenu() {
  global $PathString;
  
  //------------------------------------------------------------------------
  // Standard callXML routines.  Play a menu and get digits from the caller.
  //------------------------------------------------------------------------

  echo "<inputdigits label=\"NameByDigits\" " . 
       "repeat=\"3\" " . 
	   "cleardigits=\"TRUE\" " . 
	   "format=\"audio/wav\" " .
	   "value=\"" . $PathString . "mainmessage.wav\" " .
       "var=\"digits\" " . 
  	   "maxdigits=\"6\" " . 
	   "termdigits=\"#\" " .
	   "includetermdigit=\"FALSE\" " .
	   "maxtime=\"30s\" " . 
	   "maxsilence=\"10s\" >";
  
  echo "<ontermdigit value=\"#\">";
  echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\" />";
  echo "</ontermdigit>";

  echo "<onmaxdigits>";
  echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\" />";
  echo "</onmaxdigits>";  
  
  echo "<onmaxtime>";
  echo "<playaudio value=\"YouHaveTakenTooMuchTime.wav\"/>";
  echo "<hangup/>";  
  echo "</onmaxtime>";
   
  echo "<onmaxsilence>";
  echo "<playaudio value=\"YouHaveTakenTooMuchTime.wav\"/>";
  echo "<hangup/>";
  echo "</onmaxsilence>";

  echo "</inputdigits>";
}  // MainMenu function

//----------------------------------------------------------------------------------------

function GeneratecallXMLFromSearch() {
  global $PathString;
  global $HTTP_GET_VARS;
  
  //-------------------------------------------------------------------------------
  // Notice the digit-to-letter relationship.  The same as a standard US telephone.
  //-------------------------------------------------------------------------------
  
  $DigitArray    = Array();
  $DigitArray[2] = "[A-C]";
  $DigitArray[3] = "[D-F]";
  $DigitArray[4] = "[G-I]";
  $DigitArray[5] = "[J-L]";
  $DigitArray[6] = "[M-O]";
  $DigitArray[7] = "[P-S]";
  $DigitArray[8] = "[T-V]";
  $DigitArray[9] = "[W-Z]";
    
  //-----------------------------------
  // Begin building the callXML script
  //-----------------------------------
  
  echo "<block label=\"Results\" repeat=\"1\" cleardigits=\"TRUE\">";
  
  $TempConnection = OpenDatabase("namebydigitsdb");
  $Digits         = $HTTP_GET_VARS["digits"];
  
  //--------------------------------------------------
  // Build up a search pattern for the SQL statement
  //
  // For those not familiar with the SQL like command,
  // each set of bracketed letters acts as a wildcard
  // range.  Thus, [A-C][A-C][A-C] will return any
  // matching string in the column that has its first
  // three letters as being A, B, or C for each letter.
  // The "%" sign after the brackets acts as a wildcard
  // for the rest of the string.  
  //--------------------------------------------------
  
  $LikeStatement = "";
  
  for ($Loopy = 0; $Loopy < (strlen($Digits)); $Loopy++) {
    $LikeStatement = $LikeStatement . $DigitArray[$Digits[$Loopy]];
  }  // for statement
  
  //----------------------------------------------------------------
  // If someone entered digits that do not correspond to letters
  // (for example, the * key, the 1 key, or the 0 key), then we tell
  // the caller there were no matches, and return to the main menu.
  //----------------------------------------------------------------
  
  if ($LikeStatement == "") {
    echo "<playaudio format=\"audio/wav\" " .
	     "value=\"" . $PathString . "nomatches.wav\"/>";	
	echo "<assign var=\"digits\" value=\"\"/>";
	echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\"/>";	
	echo "</block>";
  }  // if statement
  else {
    $Loopy = 0;
	$Text  = "";
	
	//-------------------------------------------------------------------
	// Here we search by lastname.  Obviously this would be changed to 
	// firstname, or name, or whatever the database in question is
	// set up as.  For our purposes, lastname is the best search default.
	//-------------------------------------------------------------------
	
	$SQLString    = "select * from namebydigitsdb where lastname like '" . $LikeStatement . "%'";
    $TempDatabase = ReadDatabase( $TempConnection, $SQLString );
					
    while( $TempRow = FetchRowFromDatabase( $TempDatabase ) ) {
	  $Loopy++;
	 
      $FirstName = GetResultFromRow( "FIRSTNAME", $TempRow, $TempDatabase );
      $LastName  = GetResultFromRow( "LASTNAME",  $TempRow, $TempDatabase );
      $Phone     = GetResultFromRow( "PHONE",     $TempRow, $TempDatabase );
	  
	  //-----------------------------------------------------------
	  // The commas at the end are for pausing effect between names
	  //-----------------------------------------------------------
	  
	  $Text = $Text . "Press " . $Loopy . " for " . $FirstName . " " . $LastName . ",,,";
	  
	  //--------------------------------------------------------------------
	  // We are assigning a callXML variable to pass along so we don't have
	  // to hit the database again after we jump to the next page where it
	  // actually attempts to place the call.  We will ultimately pass a 
	  // variable called "selection" which will have the number the user 
	  // pressed, then there will be a list of variables starting at 
	  // "variable1" and going to "variablen" where n is the last possible
	  // selection.  If the user chooses menu option 4, then the phone 
	  // number to call will be stored in "variable4".
	  //--------------------------------------------------------------------
	  
	  echo "<assign var=\"variable" . $Loopy . "\" value=\"" . $Phone . "\"/>";
	}  // while statement
	
    CloseDatabase($TempConnection);	
	
	//-----------------------------------------------------
	// Check to see if any records in our database matched;
	// if not, then go back to the main menu.
	//-----------------------------------------------------
	
	if ($Loopy == 0) {
      echo "<playaudio format=\"audio/wav\" " .
	       "value=\"" . $PathString . "nomatches.wav\"/>";
	  echo "<assign var=\"digits\" value=\"\"/>";
	  echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\"/>";
	  echo "</block>";
	}  // if statement
	else {
	  echo "<playaudio format=\"audio/wav\" " .
	       "value=\"" . $PathString . "searchresults.wav\"/>";
		   
	  //-------------------------------------------------------
	  // We are requiring the user to listen to the entire list
	  // before being able to make a selection right now.
	  //-------------------------------------------------------
	  
	  echo "<text cleardigits=\"TRUE\" termdigits=\"\">";
	  echo $Text;
	  echo "</text>";
	  
	  echo "</block>";
	  
	  echo "<inputdigits repeat=\"1\" " .
	       "format=\"audio/wav\" " .
	       "value=\""  . $PathString . "makeyourselection.wav\" " .
		   "termdigits=\"#*\" " . 		  
           "var=\"selection\" " .
	       "maxdigits=\"3\" " .
		   "cleardigits=\"FALSE\" " .
		   "maxtime=\"30s\" " .
		   "maxsilence=\"10s\">";
		   
	  echo "<ontermdigit value=\"*\">";
      echo "<assign var=\"digits\" value=\"\"/>";
	  echo "<goto value=\"" . $PathString . "namebydigits.php\" " . 
	       "submit=\"*\" " . 
		   "method=\"get\"/>";	  
	  echo "</ontermdigit>";
	  
	  echo "<ontermdigit value=\"#\">";
	  echo "<goto value=\"" . $PathString . "namebydigits.php?call=yes\" " . 
	       "submit=\"*\" " . 
		   "method=\"get\"/>";	  
	  echo "</ontermdigit>";	
	  
	  echo "<onmaxdigits>";
	  echo "<goto value=\"" . $PathString . "namebydigits.php?call=yes\" " . 
	       "submit=\"*\" " . 
		   "method=\"get\"/>";	  
	  echo "</onmaxdigits>";		  

      echo "<onmaxsilence>";
      echo "  <playaudio value=\"YouHaveTakenTooMuchTime.wav\"/>";      
      echo "  <hangup/>";
      echo "</onmaxsilence>";
  
      echo "<onmaxtime>";
      echo "  <playaudio value=\"YouHaveTakenTooMuchTime.wav\"/>";      
      echo "  <hangup/>";  
      echo "</onmaxtime>";	
	  
	  echo "</inputdigits>";  
	}  // else statement
  }  // else statement   
}  // GeneratecallXMLFromSearch statement

//----------------------------------------------------------------------------------------

function CallTheEmployee() {
  global $PathString;
  global $HTTP_GET_VARS;
  
  //----------------------------------------------------------------
  // We have to check and see if the user entered a valid selection.
  // If they did not, then send them back to the selection menu.
  //----------------------------------------------------------------
  
  if (($HTTP_GET_VARS["variable" . $HTTP_GET_VARS["selection"]]) == "") {
    echo "<playaudio value=\"invalidNumber.wav\"/>";
	echo "<goto value=\"" . $PathString . "namebydigits.php\" " . 
	     "submit=\"*\" " . 
	     "method=\"get\"/>";	
  }  // if statement
  else {
	//-------------------------------------------------------
	// We need to assign a ParentSessionID variable so that
	// the session we call with the <run> command can send us
	// a callXML event back.  Without assigning that variable,
	// there is no way to remember/determine the parent 
	// session's ID.  Remember, \$ is the escape character for
	// $ in PHP (as seen in the line directly below...)
	//--------------------------------------------------------
	  
	echo "<assign var=\"parentsessionid\" value=\"\$session.id;\"/>";
		
	echo "<run value=\"" . $PathString . "namebydigits.php?dothecall=yes\" " .
	     "submit=\"*\" " .
		 "method=\"get\" " .
		 "var=\"DoTheCallSession\"/>";
		   
	echo "<block label=\"Hold Music\" " .
	     "repeat=\"3\">";
		   
	//-----------------------------------------------------------------
	// Play holdmusic while waiting for an event from the other session
	//-----------------------------------------------------------------
			   
	echo "<playaudio format=\"audio/wav\" " .
	     "value=\"" . $PathString . "holdmusic.wav\"/>";
		   
	//----------------------------------------------------
	// Conference the call when someone/something picks up
	//----------------------------------------------------
			
	echo "<onexternalevent value=\"success\">";
	echo "<conference targetsessions=\"\$session.eventsenderID;\"/>";
	echo "<hangup/>";
	echo "</onexternalevent>";
	  
	//-------------------------------------------
	// Inform the caller when the number was busy
	//-------------------------------------------
			
	echo "<onexternalevent value=\"busy\">";
	echo "<playaudio value=\"UserNotAvailable.wav\"/>";
	echo "<assign var=\"digits\" value=\"\"/>";							 
	echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\"/>";
	echo "</onexternalevent>";
	  
	//-------------------------------------------
	// Inform the caller when the number timedout
	//-------------------------------------------
	
	echo "<onexternalevent value=\"timedout\">";
	echo "<playaudio value=\"UserNotAvailable.wav\"/>";
	echo "<assign var=\"digits\" value=\"\"/>";		 
	echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\"/>";
	echo "</onexternalevent>";
	  
	//-----------------------------------------------
	// Inform the caller when there has been an error
	//-----------------------------------------------  
	  
	echo "<onexternalevent value=\"error\">";
	echo "<playaudio value=\"UserNotAvailable.wav\"/>";
	echo "<assign var=\"digits\" value=\"\"/>";		 					 
	echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\"/>";
	echo "</onexternalevent>";  
		
	echo "</block>";
	  
	//-------------------------------------------------------
	// In theory, we shouldn't reach this point.  But just in
	// case, we might want to give the user a message.
	//-------------------------------------------------------
	  
	echo "<playaudio value=\"UserNotAvailable.wav\"/>";
	echo "<assign var=\"digits\" value=\"\"/>";			 					 
	echo "<goto value=\"" . $PathString . "namebydigits.php\" submit=\"*\" method=\"get\"/>";  
  }  // else statement
}  // CallTheEmployee function

//----------------------------------------------------------------------------------------

function DoTheCall() {
  global $HTTP_GET_VARS;
  
  //----------------------------------------------------
  // This function does the actual outbound call, and
  // then sends back the results via callXML <sendevent>
  // actions.
  //----------------------------------------------------
  
  $NumToCall = $HTTP_GET_VARS["variable" . $HTTP_GET_VARS["selection"]];
  
  echo "<block>";

  echo "<call value=\"" . $NumToCall . "\" " .
       "maxtime=\"20s\"/>";
		
  //-----------------------------------------------------
  // Send back the appropriate event to the other session
  //-----------------------------------------------------
		
  echo "<onanswer>";
  echo "<sendevent value=\"success\" " .
       "session=\"\$parentsessionid;\"/>";
  echo "<wait value=\"Unlimited\"/>";			   
  echo "</onanswer>";
  
  echo "<oncallfailure>";
  echo "<sendevent value=\"busy\" " .
       "session=\"\$parentsessionid;\"/>";
  echo "</oncallfailure>";
  
  echo "<onmaxtime>";
  echo "<sendevent value=\"timedout\" " .
	   "session=\"\$parentsessionid;\"/>";
  echo "</onmaxtime>";
  
  echo "<onerror>";
  echo "<sendevent value=\"error\" " .
	   "session=\"\$parentsessionid;\"/>";
  echo "</onerror>";
  
  echo "</block>";
}  // DoTheCall function

//----------------------------------------------------------------------------------------

//---------------------------------------------------------
// The following are abstracted database routines.  If you
// are using Windows ODBC database drivers, use the include
// file called 'database-win32.inc'.  If you are using
// an Interbase DB under unix/linux, then use the include
// file called 'database-linux.inc'.
//---------------------------------------------------------

// include('database-linux.inc');
include('database-win32.inc');


include('passwords.inc');



$PathString = "http://appserverdev.voxeo.com/NameByDigits/";


Initialize();

//----------------------------------------------------------------------
// If digits were passed in from a previous iteration of the application
// then we are searching the database for a match.  Otherwise display
// the "Main Menu" where it asks the caller for digits to search by.
//----------------------------------------------------------------------

if     ($HTTP_GET_VARS["dothecall"]  == "yes") { DoTheCall();                 }
elseif ($HTTP_GET_VARS["call"]       == "yes") { CallTheEmployee();           }  
elseif ($HTTP_GET_VARS["digits"]     != ""   ) { GeneratecallXMLFromSearch(); }
else                                           { MainMenu();                  }

Closure();

?>