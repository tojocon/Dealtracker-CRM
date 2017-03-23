<?php 
/*                                                                              
 ****************************************************************************                                                                                
****************************************************************************                                                                                
 * Filename: skeleton_form.php -> manage_templates
 * 
 * Purpose: For mail templates in dealtracker: processes data forms through action modes: add, edit, delete
 *                                                             
 * Created: 2/21/06    
 * 
 * Last Change: 7/31/12 - customized for dealtracker 
 *  
 * Authors:   Toby Jones      
 *                                                                              
 ************************************************************************/   

 //***** Access Security Section *********//  
require '../ext_app_sec/db_connect.php';
require '../ext_app_sec/check_login.php';


$_SESSION["form_rec"] = $_POST; // try to get preload to work on past search terms

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
      header('Location: ../ext_app_sec/login.php');
      }   //end inner else
    
}  //end outer else
                    
  
 //***** END Access Security Section *********//   
  


  
//require '../ext_app_sec/db_connect.php';
//require '../ext_app_sec/check_login.php';



                                                                          
// custom variables section
// Ideally you can define your custom table and primary key here and the rest just works

$maintable="mail_templates";

$primary_key="template_id";

$shortname = "template_title"; //used with select box for edit and delete
// End custom variables section



require 'db_connect.php';




// initialize flags and variables;
$_SESSION["debug"]=0;



  

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
       
       if ($_POST[$primary_key]==0){ // 0 for new
          create();
       }
       
       
       
    	 save(); //update the data
    	 edit(); //reload the new data
    	 
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
    if ($_POST["mode"]=="change_primary"){
       

        
        $_SESSION[$primary_key]=$_POST[$primary_key];
        
      //  echo "in mode:<p>_SESSION[primary_key]=".$_SESSION[$primary_key];
        
        
        if ($_POST[$primary_key]== 0){ //new
           unset($_SESSION["form_rec"]);
        }else{
          edit();
        }
       
    }
} else{ //end if isset(mode)
    // initialize and preload for_rec //8.24.11
   // edit();
   $_SESSION["form_rec"] = '';
   $_SESSION[$primary_key]= 0;
 }
// END action event handler


//************************* ***********************************
//  function: init()
//  
//     
//**********************************************************/






//************************* ***********************************
//  function: create()
//  inserts an empty record into addr_forms to generate a form_id to use in save()
//     
//**********************************************************/
function create(){
      
       global $db_object;
       global $maintable;
       global $primary_key;
      
    //insert a new empty record just to get a new form_id 
     $qs="INSERT INTO ".$maintable."(".$primary_key.",linkbuilder_id) values(0,".$_SESSION["loggedin_linkbuilder_id"].");";
     //  require('db_connect.php');
     
		  $result=mysql_query($qs);
		if (mysql_error()){
    	   die("DB error in $qs");
      }
      // get that form_id just created
      $qs="Select ".$primary_key." from ".$maintable." order by ".$primary_key." desc limit 1;";
		  $id_set=mysql_query($qs);
		if (mysql_error()){
    	   die("DB error in $qs");
      }
      
       $id_rec = mysql_fetch_array($id_set, MYSQL_ASSOC);                                     
      
      //set session var to use in save()
      $_SESSION[$primary_key]=trim($id_rec[$primary_key]);
      
      
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
    
   $qs = "UPDATE ".$maintable." SET ";
  // $qs=$qs." doc_group_id=".$_SESSION["doc_group_id"].",";
   
   // print_r($_POST);
    foreach($_POST as $key=>$value){
		if (strlen($value) > 0 && $key != "mode" && $key != $primary_key ){
		    // append this $_POST to querystring 
		    
		          $value=mysql_real_escape_string($value);
		    
		         $qs=$qs."$key='$value',"; 
		  
		}// end if
	  
   }//end foreach
   // clip trailing comma at end of list
    $qs=substr($qs,0,(strlen($qs)-1)); 
    
    
	  $qs=$qs." WHERE ".$primary_key."=".$_SESSION[$primary_key].";";
	 
	 
	  //echo "<p>save qs=".$qs;
	 
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
     global $primary_key;
      // load an initialized address form for editing
	  $qs="select * from ".$maintable." where ".$primary_key."=".$_SESSION[$primary_key];
     
    // echo "<p>edit qs=".$qs;    
    
    $form_set=mysql_query($qs);
      
		  if (mysql_error()){
    	   die("DB error in $qs");
      }     
      if ( mysql_affected_rows()==0 ){
         echo("<p>$primary_key not in records:".$_SESSION[$primary_key]);
      }
      
      // form_rec to be used by preLoad()
      //$form_rec=$form_set->fetchrow();
      $form_rec = mysql_fetch_array($form_set, MYSQL_ASSOC);  
      // The whole record array is stored in this session var
      $_SESSION["form_rec"]=$form_rec;
      
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
     
      $_SESSION["form_rec"] = '';
      $_SESSION[$primary_key]= 0;
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
           echo 'value="';
           echo trim($fr[$feild]);
           echo '" ';     
      } 
  }
  
  //use for select boxes
  function preLoadOption($feild){
       
       $fr=$_SESSION["form_rec"];
       
       
       if  (isset($fr[$feild]) ){
           echo trim($fr[$feild]);     
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
       
       /*DBG*/// if ($_SESSION["debug"]==1) {echo "<p>owner_name:".$fr["owner_name"];}
       
       if  (isset($fr[$feild]) ){
           echo stripslashes(  trim($fr[$feild]));     
      } 
  }
  
  
  
  
  
  
  
  
  
  
  
 
  
 ?>
     
<html>
 <head>
 <title>Manage Templates</title>
 <script lang="javascript">
 
/********** action event handlers *****************/
 
 
  function doCreate(){
    // validate all
 	document.forms.mainform.mode.value="create";
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
  if(confirm("You will lose any existing data stored in this record. Are you sure you want to remove it?" )){
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
 
  </head>
<body>
<center>

<p>


<center>
<h4>Manage Templates</h4>
<form name="mainform" method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
<input type="hidden" name="mode">





<table id="outer" style="background:#FFFFCC" > <tr> <td>

		<table cellpadding=3 style="background:#CCFF99">
		<tr><td align="right"><font color="green">Working With</font>:</td><td> 
		
		<?// echo "<p>before draw loop:_SESSION[primary_key]=".$_SESSION[$primary_key]; ?>
		
		<select onchange="changePrimaryKey()" name=<?php echo $primary_key;?> >
		<option value=0> New Email Template
		<?
		////////////////////////////
		//
		//   TODO:  customize query
		//
		//////////////////////////
		$qs="select $primary_key,$shortname from " .$maintable;
    $qs .= " WHERE linkbuilder_id =".$_SESSION["loggedin_linkbuilder_id"];
    $qs .= " order by $shortname";
		
		
		
		$f_set=mysql_query($qs);
		
		if(mysql_error()) {
		
			echo("error in select fill: ".mysql_error());
			//die($f_set->getMessage() );
		}
		
		  
		while($f_rec= mysql_fetch_array($f_set, MYSQL_ASSOC)  ){
		    
		    if ($_SESSION[$primary_key]==$f_rec[$primary_key]){
		        echo "<option value=".$f_rec[$primary_key]." selected>".$f_rec[$shortname]."\n";
		    }else{
		        echo "<option value=".$f_rec[$primary_key].">".$f_rec[$shortname]."\n";
		    }
		    
		    
		}//end while
		
		
		?>
		</td>
    
    <td>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <button onclick="doSave()">Save</button>
&nbsp;&nbsp;&nbsp;&nbsp;
<button onclick="doDelete()">Delete</button>
    </td>
    
    
    </tr>
		</table>
		
		 <table cellpadding=3>
		<tr><td align="right">
    
   
    Template Title:</td><td><input name="<? echo $shortname; ?>" <? preLoad($shortname); ?> size="70"><font color="red">*</font></td></tr>
		
		
	<!--	<tr><td align="right">Message Subject:</td><td> <input name="message_subject"  <? preLoad("message_subject"); ?> size="50"> </td></tr> -->
	
	
	
	<tr><td align="right">Message Subject:</td><td> <textarea rows="1" cols="60" name="message_subject"><? preLoadTextarea("message_subject"); ?> </textarea>
    
     
    
     </td></tr>
	 
	
	
	
  <tr><td align="right">Load when Actions Status is:</td><td>
      <select name="actions_status"> 
     <option value="<? preLoadOption("actions_status"); ?>" selected> <?preLoadOption("actions_status"); ?></option>
     <option value=" " >Any Actions</option>
     
	<?php
	   require 'actions_status_options.php';
	?>
     
  </select></td></tr>
	
		<tr><td align="right">Message Body:</td><td> <textarea rows="20" cols="60" name="message_body"><? preLoadTextarea("message_body"); ?> </textarea>
    
     
    
     </td></tr>
		
	
		
		</table>
		
</td><td>



</td></tr>	</table>

    <strong> Placeholders:</strong>
<br>[domain_name]
<br>[domain_name_base] 
<br>[article_location]	
<br>* Make sure article_location data isn't blank if you use that.
<br>** domain_name_base is same as domain_name but no [.com].
<p>
<!--<button onclick="doCreate()">Create New Field</button>-->
 <a href="javascript:self.close()">Close</a>
 <br>
 
<?
//print_r($_SESSION);

unset($_SESSION["form_rec"]);
//echo "<br>_SESSION[$primary_key] = ".$_SESSION[$primary_key];
?> 
