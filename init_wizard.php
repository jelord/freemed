<?php
	// $Id$
	// $Author$
	// code: fred trotter (ftrotter@synseer.com)
	// lic: GPL, v2

include_once("lib/freemed.php");
$GLOBALS['__freemed']['no_menu_bar'] = true;
 
   // this is the begining of a wizard init system.
   // this is the most important page to secure because access to it
   // will allow a user to destroy the database.
   // As a result there will be two security functions added in 

   //1. IP based authentication with the default set to localhost.
   //2. The database user and password must be regiven.

   // 1. Means that by default it will be impossible to initalize the database
   // from any other hosts. Most people will be installing on a local X config so this will be easy
   // but for those ssh fans this wizard will need to be lynx friendly
   // however if an end-user wants to make it less secure and allow "anywhere" configuration
   // that will be allowed, but the defaults will still be secure

   // 2. Means that the person using the web interface has access to the database , and settings.php
   // effectivley demonstrating that the web interface is actually a lower level of interface for this 
   // user. He is using the wizard for convience and he is not a web hacker using it to escalate privileges

   // TODO 
   // 1. Add code to enforce timed login!!

$page_name = "init_wizard.php";   

// IP based authentication check

if(0!=strcmp($_SERVER['REMOTE_ADDR'],INIT_ADDR)){	
	include_once("errors/init_admin_ipaddr.php");	
	die("<BR>FreeMED expects the intial setup to be done from the localhost. Dying b/c your IP is not in settings.php as INIT_ADDR");
}

if ($action=="login") {     
    $display_buffer .= "
<div ALIGN=\"LEFT\">
	
	This is the the wizard to setup the admin account <BR>
	Before creating that account you must prove that you <BR>
	Have the appropriate level of access, please provide <BR>
	The database user name and password, as found in settings.php

</div>

<p/>
<table WIDTH=\"100%\" BORDER=\"0\" CELLPADDING=\"2\">
<tr>  <td ALIGN=\"RIGHT\">
      <form ACTION=\"init_wizard.php?action=auth\" METHOD=\"POST\">

      <input TYPE=\"HIDDEN\" NAME=\"__dummy\"
      VALUE=\"01234567890123456789012345678901234567890
            01234567890123456789012345678901234567890
            01234567890123456789012345678901234567890\"/>
       ".__("Database Username")." :
      </TD><TD ALIGN=\"LEFT\">
      <input TYPE=\"TEXT\" NAME=\"_username\" LENGTH=\"20\" MAXLENGTH=\"32\"/>
      </td>
</tr>
<tr>    <td ALIGN=\"RIGHT\">
        ".__("Database Password")." :
        </td>
         <td>
             <input TYPE=\"PASSWORD\" NAME=\"_password\" LENGTH=\"20\" MAXLENGTH=\"32\"/>
         </td>
</tr> 
<tr>    <td ALIGN=\"RIGHT\">
        ".__("Admin Account Password")." :
        </td>
         <td>
             <input TYPE=\"PASSWORD\" NAME=\"_adminpassword1\" LENGTH=\"20\" MAXLENGTH=\"32\"/>
         </td>
</tr> 
<tr>    <td ALIGN=\"RIGHT\">
        ".__("Confirm Admin Account Password")." :
        </td>
         <td>
             <input TYPE=\"PASSWORD\" NAME=\"_adminpassword2\" LENGTH=\"20\" MAXLENGTH=\"32\"/>
         </td>
</tr> 
</table>
<div ALIGN=\"CENTER\">
  <input TYPE=\"SUBMIT\" VALUE=\"".__("Sign In")."\" CLASS=\"button\" />
  <input TYPE=\"RESET\"  VALUE=\"".__("Clear")."\" CLASS=\"button\" />
</div>
</form>
";
	
// drop to the page...  
template_display();

}

if($action=="auth")
{

	//lets display the banner!!

	// Lets check IP addresses again, otherwise people will 
	// Try to go directly to this page!!

	if(0!=strcmp($_SERVER['REMOTE_ADDR'],INIT_ADDR)){	
		die(__("Page Not Accessible from your IP Address")."<br/>");
	}

	// time constraint psuedo code
	// if (you attempted to login less than a minute ago)
	// {
	// die (" you have to wait 1 min between logins");
	// set database lastlogintime = now;
	// }


	if((0!==(strcmp(DB_USER,$_REQUEST['_username']))) or
	(0!==(strcmp(DB_PASSWORD,$_REQUEST['_password'])))) {
		// impose a time penalty here...
		// something like 30 sec for the first...
		// 1 min for two or more...
		// or hell just 1 min...		

		// set database lastlogintime = now;
		die( __("Incorrect user/password combination")." 1");

	}

	if(0!==(strcmp($_REQUEST['_adminpassword1'],$_REQUEST['_adminpassword2'])))
	{
		die( "admin passwords to not match");
		// no time setting here, if they know the database password
		// then this is just an honest mistake!!
	}
	
	// here I enter the new admin account into the database!!
	$display_buffer .= __("Database Password Accepted")."... <br/>\n";

// these should eventually be connected to a die() command!!
$this_user = CreateObject('FreeMED.User');

$md5_pass=md5($_REQUEST['_adminpassword1']);
	
$this_user->init($md5_pass);

	$display_buffer .= "
<div ALIGN=\"LEFT\">	
".__("User table created.")."<br/>
".__("Admin password set.")."<br/>
".__("You will now be returned to the the login prompt.")."<br/>
".__("You can login using:")."<br/><br/>
username=admin <br/>
password=WHAT_YOU_JUST_ENTERED <br/><br/>
replace WHAT_YOU_JUST_ENTERED with the admin password that you just created <BR>
</div>";
  	header("Refresh: 30;url=index.php");	
	template_display();
}

?>
