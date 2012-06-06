<?PHP

//---------------------------------------------------------------------------
// ---------------------------------------------------
// copyright 2000 by voxeo corporation. (see LGPL.txt)
// ---------------------------------------------------
//
// v1.0 Coded in PHP 4.0
// v1.0 Coded by Stephen J. Lewis
//
// This include file is used with the NameByDigits application and
// is a simple list of HTTP/FTP username and password abstractions.
// If you want to have usernames and passwords on your server, simply
// input the values below (or change them to use HTTP_GET_VARS, etc.
//---------------------------------------------------------------------------

function GetHTTPPassword() {
  //-------------------------------------------------------
  // This function simply allows you to change the password
  // for HTTP requests.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Password = "";
  return $Password;
  
}  // GetHTTPPassword function

//---------------------------------------------------------------------------

function GetHTTPUsername() {
  //-------------------------------------------------------
  // This function simply allows you to change the Username
  // for HTTP requests.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Username = "";
  return $Username;
  
}  // GetHTTPUsername function

//---------------------------------------------------------------------------

function GetFTPPassword() {
  //-------------------------------------------------------
  // This function simply allows you to change the password
  // for FTP requests.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Password = "pingpong";
  return $Password;
  
}  // GetFTPPassword function

//---------------------------------------------------------------------------

function GetFTPUsername() {
  //-------------------------------------------------------
  // This function simply allows you to change the Username
  // for FTP requests.  Edit the value below as needed.
  //-------------------------------------------------------
  
  $Username = "opensource";
  return $Username;
  
}  // GetFTPUsername function

//---------------------------------------------------------------------------


?>