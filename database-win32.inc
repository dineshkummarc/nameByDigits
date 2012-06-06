<?PHP

// ---------------------------------------------------
// copyright 2000 by voxeo corporation. (see LGPL.txt)
//
// v1.0 Coded in PHP 4.0
// ---------------------------------------------------

//---------------------------------------------------------------------------

function GetDatabasePassword() {
  //-------------------------------------------------------
  // This function simply allows you to change the password
  // for your database.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Password = "";
  return $Password;
  
}  // GetDatabasePassword function

//---------------------------------------------------------------------------

function GetDatabaseUsername() {
  //-------------------------------------------------------
  // This function simply allows you to change the Username
  // for your database.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Username = "sa";
  return $Username;
  
}  // GetDatabaseUsername function

//---------------------------------------------------------------------------

function OpenDatabase($DatabaseName) {
  //-----------------------------------------------------
  // Opens our database; returns the connection variable.
  //-----------------------------------------------------
  
  $TempConnection = odbc_connect( $DatabaseName,  
  								  GetDatabaseUsername(),  
								  GetDatabasePassword() ); 
  
  if (!$TempConnection) { 
    ErrorHandler( "Error in odbc_connect" , $TempConnection ); 
  }  // if statement 
  
  return $TempConnection;
}  // function statement

//---------------------------------------------------------------------------

function ReadDatabase($TempConnection, $TempSQL) {
  //-------------------------------------------------------------------------------------
  // Queries the database sends in a connection variable, SQL string, returns a record
  //-------------------------------------------------------------------------------------

  $TempDatabase = odbc_exec( $TempConnection, $TempSQL ); 
  
  if (!$TempDatabase) { 
    ErrorHandler( "Error in odbc_exec( no cursor returned ) " , $TempDataBase ); 
  }  // if statement  
  
  return $TempDatabase;
}  // function statement

//---------------------------------------------------------------------------

function CloseDatabase($TempConnection) {
 
  odbc_close( $TempConnection );
  
}  // function statement

//---------------------------------------------------------------------------

function ErrorHandler( $Message, $TempConnection ) { 
    echo $Message; 
    odbc_close( $TempConnection ); 
    exit(); 
}  // function statement

//------------------------------------------------------------------------------------

function FetchRowFromDatabase( $TempDatabase ) {

  if (odbc_fetch_row( $TempDatabase )) { return $TempDatabase; }
  else                                 { return FALSE;         }

}  // FetchRowFromDatabase function

//------------------------------------------------------------------------------------

function GetResultFromRow( $ColumnName, $TempRow, $TempDatabase ) {
  //-------------------------------------------------------------
  // For the Win32 odbc resource, the TempRow variable is ignored
  //-------------------------------------------------------------
  
  $TempVariable = odbc_result($TempRow, $ColumnName);
  
  return $TempVariable;
}  // GetResultFromRow function

?>
