<?php 
/*                                                                              
 ****************************************************************************                                                                                
 * Filename: skeleton_form.php -> form1.php
 * 
 * Purpose: processes data forms through action modes: add, edit, delete
 *          for dealtracker software 
 *                                                             
 * Created: 9/1/11   
 * 
 * Last Change: 11.28.11  - removed auto-load [was too slow]
 *  
 * Author:   Toby Jones      
 *                                                                              
 ************************************************************************/   

 session_start();
  
  
  //prevent_short_logoff(); // this will load a recent access from thsi ip and load the profile from server side temo file containing username and password is time is less than 5 secs and ip matches.
  
  
//require '../ext_app_sec/db_connect.php';//already there
require '../ext_app_sec/check_login.php';



  if ($logged_in == 1){
   //membership is valid
   
}else{
  // die("You dont have access.");
}

$mygroup= "linkbuilders";
if (strstr($_SESSION["access_groups"], $mygroup)){
   //membership is valid
   $active_group="linkbuilders";
}else{
  // echo ("You are not a member of this access group, or your session expired.");
  //header( '/ext_app_sec/login.php');
  
  if (strstr($_SESSION["access_groups"], "linksourcers")){
          $active_group="linksourcers";
  
   } else{
      //die ("died at condition :  if (strstr($_SESSION[access_groups], linksourcers)){");
      header('Location: ../ext_app_sec/login.php?site='.$_GET["site"]);
      }   //end inner else
    
}  //end outer else

                                                                          
// custom variables section
// Ideally you can define your custom table and primary key here and the rest just works

$maintable="opportunities";

$primary_key="domain_name";

$shortname = "domain_name"; //[NOT ANYMORE] used with select box for edit and delete
// End custom variables section


 if (strstr($_SESSION["access_groups"], 'linkbuilders-TeamV')){
     require 'db_connect_teamv.php';
 }else{
  require 'db_connect.php';
}





 //verify loggedin_linkbuilder_id  
if ( strlen($_SESSION["loggedin_linkbuilder_id"])  < 1 ){

    $qs="SELECT linkbuilder_id from linkbuilders where name_first like '".$_SESSION["peast_username"]."'";


     
    $logged_set=mysql_query($qs);
      
		  if (mysql_error()){
    	   echo("DB error in edit() $qs");
      }  
	  
      if ( mysql_affected_rows()==0 ){
         echo("<p>_SESSION[peast_username] not in dealtracker records:".$_SESSION["peast_username"]);
      }
      
      if ( mysql_affected_rows() > 1 ){
         echo("<p>_SESSION[peast_username] occurs more than once in dealtracker db:".$_SESSION["peast_username"]);
      }
    
    
      $logged_rec = mysql_fetch_array($logged_set, MYSQL_ASSOC);
     
      $_SESSION["loggedin_linkbuilder_id"] = $logged_rec["linkbuilder_id"]; 
     
} //end if loggedin_linkbuilder_id nto set
  
 
  
  
  



 // substring incoming site domain paramater - defeat most inject attempts 
$site=substr( trim($_GET["site"]), 0, 96);

$site=str_ireplace('http://','', $site);

$site=str_ireplace('https://','', $site);

$site=str_ireplace('www.','', $site);

$mark=stripos( $site,'/');

//$domain=substr($site, 0,$mark+1); //old way leaves trailing slash
$domain=substr($site, 0,$mark);


 // clean domain subdomain

 /*$parts=explode('.',$domain);
 $count= count($parts);
 
 $domain= $parts[$count-2].'.'.$parts[$count-1];

  */

if ($domain !==  $_SESSION[$primary_key]){
      //this is a new page loaded
       $_SESSION["savetime"] = '0';
  }
  
  
  
if(strlen($domain) > 3 ){   // do we have a GET param?
    $_SESSION[$primary_key]=$domain; 
}else{    // assign the session var back in
    $domain=$_SESSION[$primary_key]; 
}




  // echo "<br>Domain: ".$domain;

  //  echo "<br>Domain SESSION: ". $_SESSION["form_rec"]["domain_name"];
  //  echo "<br>name SESSION: ". $_SESSION["form_rec"]["name"];
// initialize flags and variables;
$_SESSION["debug"]=0;
 

// echo "<br> before init domain=".$domain;
 //    print_r($_SESSION["form_rec"]);
  init();

//************************* ***********************************
//   BEGIN  action event form handler code   
//**********************************************************/
// The mode value SHOULD be a descriptive string of what action (button click)
// we are doing.

if (isset($_POST["mode"]) || isset($_GET["mode"])){
     
     
    //****** Create ***********//
   /* if ($_POST["mode"]=="create"){
    	create(); //create th einitial record
    	 save();
    	
    } */        
    
    //****** SAVE ***********//
    if ($_POST["mode"]=="save"){
       
      // if ($_POST["linkbuilder_id"]==0){ // 0 for new
     //  if( strlen($_SESSION["form_rec"]["domain_name"]) < 1   ) {
        
          // will fail if already exists
          create();
      // }
       
       
       
    	 save(); //update the data
    	 edit(); //reload the new data
    	 
    	 
    	 $_SESSION["savetime"]= date("H:i:s");
    	 //refesh existin view
    	 
    	 echo '<script lang="javascript"> parent.parent.main.location.href="'.$_SERVER["PHP_SELF"].'";';
    	 echo "</script>";
    	
    }
    
    
    //****** EDIT ***********//
    if ($_POST["mode"]=="edit"){
        
       // $_SESSION[$primary_key]=$_POST[$primary_key]; - create() needs to set this otherwise could be 0
       
       	edit();   
    }

    
    //****** DELETE ***********//    
    if ( $_POST["mode"]=="delete"){
    	// delete target form
      delete(); 
    }
    
    
    //****** change_primary***********//
   /* if ($_POST["mode"]=="change_primary"){
       

        
        $_SESSION[$primary_key]=$_POST[$primary_key];
        
        //echo "in mode:<p>_SESSION[primary_key]=".$_SESSION[$primary_key];
        
        
        if ($_POST[$primary_key]== 0){ //new
           unset($_SESSION["form_rec"]);
        }else{
          edit();
        }
       
    }    */
}//end if isset(mode)
// END action event handler







//************************* ***********************************
//  function: perm_check_opp($oppid)
//  
//     
//**********************************************************/


function perm_check_dom($domain_name){
      
       //include 'db_connect.php';
       global $db_object;

        // load an initialized address form for editing
	     $qs="SELECT domain_name from opportunities WHERE domain_name like '";
         $qs.=$domain_name."'";
         
         if( stripos($_SESSION["memb"],'linkbuilder_admin') === false){
         $qs .= " AND (linkbuilder_id=".$_SESSION["loggedin_linkbuilder_id"];
         $qs .= " OR linkbuilder_id is NULL";
         $qs .= " OR linkbuilder_id = 0)";
         }
         
         //echo "qs=".$qs;
         
         $perm_set=mysql_query($qs);

  		  if (mysql_error()){
      	   echo("DB error in edit() $qs");
        }  
  	  
        if ( mysql_affected_rows()==0 ){
           //echo("no records affected in group check");
        }
      
       $perm_rec = mysql_fetch_array($perm_set, MYSQL_ASSOC);

       if( strlen($perm_rec["domain_name"]) < 3){
       
             echo("<p>&nbsp;</p><center><h3>Be careful! You just tried to edit ".$domain_name);
             echo (", and that domain is not assigned to you.</h3> ");
             echo ('<a href="form1.php"><b>Click here to go back</b> and load domain data for a domain assigned to you, or work with an unclaimed one.</a>');
             die  ('<p>&nbsp;</p><a href="mailto:tjones@kimongroup.com"><b>Contact</b></a> Dealtracker support if you think this is an error.</center>');
       }
	         
	 
	 
}// end function init()



//************************* ***********************************
//  function: init()
//  
//     
//**********************************************************/


function init(){
      
       global $db_object;
       global $maintable;
       global $primary_key;
        global $site,$domain;
      
     // echo "<br> in init()1 domain=".$domain;
      
    $qs= "select * from opportunities p, linkbuilders b ";
  $qs .= "where domain_name like '".$domain."' "; 
  
  // need to insert an fake linkbuilder id for 'unclaimed' to get released ones
  $qs .= "AND p.linkbuilder_id = b.linkbuilder_id "; 


      //echo " <br>qs=$qs";
     
		  $id_set=mysql_query($qs);
		 
		 $id_rec = mysql_fetch_array($id_set, MYSQL_ASSOC); 
     // $id_rec=$id_set->fetchrow();
      
      
      //set session var to use in save()
      //$_SESSION[$primary_key]=trim($id_rec[$primary_key]);
          $id_rec["domain_name"]=$domain;
      $_SESSION["form_rec"]=$id_rec;
       $_SESSION[$primary_key]=$domain;
       //echo "<br>------------------" ;
             //   print_r($_SESSION["form_rec"]);
      // echo "<br> ending init() _SESSION[form_rec]notes=".$_SESSION["form_rec"]["notes"];
        // echo "<br> in init()2 domain=".$domain;
}// end function init()



//************************* ***********************************
//  function: create()
//  inserts an empty record into addr_forms to generate a form_id to use in save()
//     
//**********************************************************/
function create(){
      
       global $db_object;
       global $maintable;
       global $primary_key;
       global $domain;
      
    //insert a new empty record just to get a new form_id 
     $qs="INSERT INTO ".$maintable."(opp_id,domain_name,finder) values(0,'".$domain."','".$_SESSION["peast_username"]."');";
     //  require('db_connect.php');
     
		  $result=mysql_query($qs);
	/*	if (mysql_error()){
        echo("DB error in create() $qs");
      }
      */
      // get that form_id just created
     // $qs="Select  from ".$maintable." order by opp_id desc limit 1;";
		 // $id_set=mysql_query($qs);
	/*	if (mysql_error()){
		     echo " Save Failed <br>That domain may be claimed already.<br>";
    	   echo  mysql_error();
         echo ("DB error 1 in $qs");
      }
      
     //  $id_rec = mysql_fetch_array($id_set, MYSQL_ASSOC);                                     
      
      	if (mysql_error()){
      	 echo  mysql_error(); 
		     echo " Save Failed <br>That domain may be claimed already.<br>";
    	   echo ("DB error 1 in $qs");
      }
        */
      //set session var to use in save()
      //$_SESSION[$primary_key]=trim($id_rec[$primary_key]);
      //echo "<br>in create() domain=".$domain;
      
      
}// end function create()

//************************* ***********************************
//  function: save()
//  updates table ".$maintable." with  all form feilds submitted
//     
//**********************************************************/
function save(){
    global $db_object;
    global $maintable;
    global $primary_key;
    global $domain;
    
    
    perm_check_dom($domain); //die if no perms
    
    //echo "<br> insave() domain=".$domain;
    
   $qs = "UPDATE opportunities SET ";
  // $qs=$qs." doc_group_id=".$_SESSION["doc_group_id"].",";
   
   //print_r($_POST);
   
    foreach($_POST as $key=>$value){
		if (strlen($value) > 0 && $key != "mode" && $key != $primary_key && $key != "last_action" ){
		    // append this $_POST to querystring 
		         $value=trim($value);
		         $qs=$qs."$key='$value',"; 
		  
		}// end if
	  
   }//end foreach
   // clip trailing comma at end of list
    $qs=substr($qs,0,(strlen($qs)-1)); 
    
    
	  $qs=$qs." WHERE ".$primary_key."='".$domain."';";
	 
	   //echo "save() qs=$qs";
	   //echo "<br>---<br>";
	 
		$result=mysql_query($qs);
		
		if (mysql_error()  ){
    	 echo ("DB error in $qs".mysql_error());
    }
    
    
}//end function save()

//************************* ***********************************
//  function: edit()
//  loads a form record into a session var to be used to preLoad the form data
//     
//**********************************************************/
function edit(){
      //include 'db_connect.php';
     global $db_object;
     global $maintable;
     global $primary_key,$domain;
      // load an initialized address form for editing
	  $qs="select * from ".$maintable." where domain_name='".$domain."'";
     
    // echo "<p>edit qs=".$qs;    
    
    $form_set=mysql_query($qs);
      
		  if (mysql_error()){
    	   error("DB error in edit() $qs");
      }     
      if ( mysql_affected_rows()==0 ){
         echo("<p>$primary_key not in records:".$_SESSION[$primary_key]);
      }
      
      // form_rec to be used by preLoad()
      //$form_rec=$form_set->fetchrow();
      $form_rec = mysql_fetch_array($form_set, MYSQL_ASSOC);  
      // The whole record array is stored in this session var
      $_SESSION["form_rec"]=$form_rec;
      
      // echo "<br>---<br>"  ;
     // echo " ending edit() _SESSION[form_rec]notes=".$_SESSION["form_rec"]["notes"];
  }
  
//************************* ***********************************
//  function: delete()
//  delete a form record
//     
//**********************************************************/

function delete(){
      //include 'db_connect.php';
     global $db_object;
     global $maintable;
     global $primary_key;
     
     $qs="delete from $maintable where $primary_key = ".$_SESSION[$primary_key];
     
     $result=mysql_query($qs);
     
		if (mysql_error()){
	    	 die("DB error in $qs");
	    }
     unset($_SESSION[$primary_key]);  
     
} //end function
     
     
     
//************************* ***********************************
//  function: preLoad()
//  renders inputbox values to the screen
//     
//**********************************************************/
  function preLoad($feild){
       
       $fr=$_SESSION["form_rec"];
       
       /*DBG*/// if ($_SESSION["debug"]==1) {echo "<p>owner_name:".$fr["owner_name"];}
       
       if  (isset($fr[$feild]) ){
           echo "value='".trim($fr[$feild])."' ";     
      } 
  }
  
  //use for select boxes
  function preLoadOption($feild){
       
       $fr=$_SESSION["form_rec"];
       
       
       if  (isset($fr[$feild]) ){
           echo ucwords(  trim($fr[$feild]) );     
      } 
  }
  
 function preLoadRadio($name, $value){
    $fr=$_SESSION["form_rec"];
    if  (strlen($fr[$name]) > 0){ 
        if (strstr($fr[$name],$value)){
            echo " checked";
        }
    }               
 } 
 
 function preLoadTextArea($feild){
       
       $fr=$_SESSION["form_rec"];
       
       
      // echo "in preLoadTextArea wirh ".$feild;
      // print_r($fr); 
       
       
       if  (isset($fr[$feild]) ){
           echo trim($fr[$feild]);     
      } 
      
      // echo " ending preLoadTextArea() _SESSION[form_rec]notes=".$_SESSION["form_rec"]["notes"];
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
 ?>
 
 <head>
 
 
 <script lang="javascript">
 
/********** action event handlers *****************/
 
 
  function doCreate(){
    // validate all
 	document.forms.mainform.mode.value="create";
 	document.forms.mainform.submit();
 }
 function doFlag(){
    // validate all
    
  
  //alert ("step1");
  document.forms.mainform.deal_status.value="Good Prospect";
 	document.forms.mainform.mode.value="save";
 	document.forms.mainform.submit();
 }
 function doSave(){
    // validate all
    
  
  //alert ("step1");
 	document.forms.mainform.mode.value="save";
 	document.forms.mainform.submit();
 }
 function doEdit(){
 	document.forms.mainform.mode.value="edit";
 	document.forms.mainform.submit();
 }
 function doDelete(){
    // prompt for confirmation on delete target
  if(confirm("You will lose any existing data stored in this field. Are you sure you want to remove it?" )){
   	document.forms.mainform.mode.value="delete";
   	document.forms.mainform.submit();
 	}//end if confirm
 }
 
 function changePrimaryKey(){
    //9/27/2007 11:02:27 AMalert("changeing primary");
  	document.forms.mainform.mode.value="change_primary";
 	  document.forms.mainform.submit();
 
 }
 /***********  VALIDATION ********************/
// use  maskKey functions at /common/functions/mask_functions.js
 
 </script>
 
 
  
<script src="calendarDateInput.js"></script>
  </head>
 
<body>
<center>

<p>


<center>
<!--session=<?php //echo session_id(); ?><br>   -->
<font color="green" size=5>DealTracker</font> (v2.0) <br>
      [user: <?php echo $_SESSION["peast_username"]; ?>]
      <hr>  
      <?php 
       
      if ( $_SESSION["firstrun"] !== 1) { 
      ?>                       
       <div style="position:absolute;top:80px;left:0px;width:400px;height:800px;background-color:#DEDEDE">
       &nbsp;<h2> Click "Load Domain Data" up top to begin </h2>
       <p> Dealtracker updated 8/9/12</p>
        <?php //echo '_SESSION["firstrun"]='.$_SESSION["firstrun"]; ?>
       </div>
       
       <?php  $_SESSION["firstrun"]=1;  } // end first run?>
<?php

if (  $active_group=="linksourcers"){
        // echo "group is linksourcers";
         require 'form_body_limited.php';
  } else {
           require 'form_body_regular.php';
  }   ?>
  <font color="blue"><a target="_content" href="http://inkpal.net/dealtracker/report1.php">Records View</a> </font>
<?
//unset($_SESSION["form_rec"]);
?> 
