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
  
  $Password = "masterkey";
  return $Password;
  
}  // GetDatabasePassword function

//---------------------------------------------------------------------------

function GetDatabaseUsername() {
  //-------------------------------------------------------
  // This function simply allows you to change the Username
  // for your database.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Username = "SYSDBA";
  return $Username;
  
}  // GetDatabaseUsername function

//---------------------------------------------------------------------------

function OpenDatabase($DatabaseName) {
  //-----------------------------------------------------
  // Opens our database; returns the connection variable.
  //-----------------------------------------------------
  
  $TempConnection = ibase_connect( $DatabaseName,  
  								  GetDatabaseUsername(),  
								  GetDatabasePassword() ); 
  
  if (!$TempConnection) { 
    ErrorHandler( "Error in ibase_connect" , $TempConnection ); 
  }  // if statement 
  
  return $TempConnection;
}  // function statement

//---------------------------------------------------------------------------

function ReadDatabase($TempConnection, $TempSQL) {
  //-------------------------------------------------------------------------------------
  // Queries the database sends in a connection variable, SQL string, returns a record
  //-------------------------------------------------------------------------------------

  $TempDatabase = ibase_query( $TempConnection, $TempSQL ); 
  
  if (!$TempDatabase) { 
    ErrorHandler( "Error in ibase_query( no cursor returned ) " , $TempDataBase ); 
  }  // if statement  
  
  return $TempDatabase;
}  // function statement

//---------------------------------------------------------------------------

function CloseDatabase($TempConnection) {
 
  ibase_close( $TempConnection );
  
}  // function statement

//---------------------------------------------------------------------------

function ErrorHandler( $Messagse, $TempConnection ) { 
  echo $Message; 
  ibase_close( $TempConnection ); 
  exit(); 
}  // function statement

//------------------------------------------------------------------------------------

function FetchRowFromDatabase( $TempDatabase ) {

  $TempRow = ibase_fetch_object( $TempDatabase );
  
  return $TempRow; 

}  // FetchRowFromDatabase function

//------------------------------------------------------------------------------------

function GetResultFromRow( $ColumnName, $TempRow, $TempDatabase ) {

  //-----------------------------------------------------------------------
  // For the Linux interbase resource, the TempDatabase variable is ignored
  //-----------------------------------------------------------------------
  
  $TempVariable = $TempRow->$ColumnName;
  
  return $TempVariable;
}  // GetResultFromRow function

?>