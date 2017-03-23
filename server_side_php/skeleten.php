<?php 
/*                                                                              
 ****************************************************************************                                                                                
 * Filename: skeleton_form.php
 * 
 * Purpose: processes data forms through action modes: add, edit, delete
 *                                                             
 * Created: 2/21/06    
 * 
 * Last Change: 10/30/07 
 *  
 * Authors:   Toby Jones      
 *                                                                              
 ************************************************************************/   
                                                                          
// custom variables section
// Ideally you can define your custom table and primary key here and the rest just works

$maintable="apps";

$primary_key="app_id";

$shortname = "shortname"; //used with select box for edit and delete
// End custom variables section



session_start();
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
        
        //echo "in mode:<p>_SESSION[primary_key]=".$_SESSION[$primary_key];
        
        
        if ($_POST[$primary_key]== 0){ //new
           unset($_SESSION["form_rec"]);
        }else{
          edit();
        }
       
    }
}//end if isset(mode)
// END action event handler


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
     $qs="INSERT INTO ".$maintable."(".$primary_key.") values(0);";
     //  require('db_connect.php');
     
		  $result=$db_object->query($qs);
		  if (DB::isError($result)){
    	   die("DB error in $qs");
      }
      // get that form_id just created
      $qs="Select ".$primary_key." from ".$maintable." order by ".$primary_key." desc limit 1;";
		  $id_set=$db_object->query($qs);
		  if (DB::isError($id_set)){
    	   die("DB error in $qs");
      }
      $id_rec=$id_set->fetchrow();
      
      
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
   
   print_r($_POST);
    foreach($_POST as $key=>$value){
		if (strlen($value) > 0 && $key != "mode" && $key != $primary_key ){
		    // append this $_POST to querystring 
		    
		    if($key=='authentication'){  //$ped is salted hash
		         /// TODO: salt and hash this
		         $hashed_value=md5($_SESSION[$primary_key].trim($value));
		         $qs=$qs."$key='$hashed_value',";
		    }else{
        			$qs=$qs."$key='$value',"; 
				}   	
		}// end if
	  
   }//end foreach
   // clip trailing comma at end of list
    $qs=substr($qs,0,(strlen($qs)-1)); 
    
    
	  $qs=$qs." WHERE ".$primary_key."=".$_SESSION[$primary_key].";";
	 
		$result=$db_object->query($qs);
		if (DB::isError($result)){
    	 die("DB error in $qs");
    }
    
    unset($_SESSION["authentication"]);
     unset($_POST["authentication"]);
    
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
    $form_set=$db_object->query($qs);
      
		  if (DB::isError($form_set)){
    	   die("DB error in $qs");
      }
      if ($form_set->numRows()==0){
         die("<p>$primary_key not found:".$_SESSION[$primary_key]);
      }
      
      // form_rec to be used by preLoad()
      $form_rec=$form_set->fetchrow();
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
     
     $result=$db_object->query($qs);
     
			if (DB::isError($result)){
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
           echo trim($fr[$feild]);     
      } 
  }
  
 ?>
 
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
 
 
<body>
<center>

<p>


<center>
<h4>Manage Web Apps</h4>
<form name="mainform" method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
<input type="hidden" name="mode">

<table id="outer"> <tr> <td>

		<table cellpadding=3>
		<tr><td align="right"><font color="green">Working With</font>:</td><td> 
		
		<?// echo "<p>before draw loop:_SESSION[primary_key]=".$_SESSION[$primary_key]; ?>
		
		<select onchange="changePrimaryKey()" name=<?php echo $primary_key;?> >
		<option value=0> New Web App
		<?
		////////////////////////////
		//
		//   TODO:  customize query
		//
		//////////////////////////
		$qs="select $primary_key,$shortname from " .$maintable;
		
		
		
		$f_set=$db_object->query($qs);
		
		if(DB::isError($f_set)) {
		
			echo("error ".$f_set->getMessage() );
			//die($f_set->getMessage() );
		}
		
		  
		while($f_rec=$f_set->fetchrow()){
		    
		    if ($_SESSION[$primary_key]==$f_rec[$primary_key]){
		        echo "<option value=".$f_rec[$primary_key]." selected>".$f_rec[$shortname]."\n";
		    }else{
		        echo "<option value=".$f_rec[$primary_key].">".$f_rec[$shortname]."\n";
		    }
		    
		    
		}//end while
		
		
		?>
		</td></tr>
		
		<tr><td align="right">Application Short Name:</td><td><input name="<? echo $shortname; ?>" <? preLoad($shortname); ?> ><font color="red">*</font></td></tr>
		<tr><td align="right">URL Path:</td><td><input name="launch_path" <? preLoad("launch_path"); ?>> <font color="red">*</font></td></tr>
		
		<!--<tr><td colspan=2 align="center">-- optional --</td></tr>-->
		<tr><td align="right">Description:</td><td> <input name="description"  <? preLoad("description"); ?>></td></tr>
		
		
		
		
		</table>
		
</td><td>



</td></tr>	</table>

	
<p>
<!--<button onclick="doCreate()">Create New Field</button>-->
<button onclick="doSave()">Save</button>
&nbsp;&nbsp;&nbsp;&nbsp;
<button onclick="doDelete()">Delete</button>
<p>
<p>
 <br>
 
<?
unset($_SESSION["form_rec"]);
?> 
