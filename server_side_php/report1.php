<?php 
/*                                                                              
 ****************************************************************************                                                                                
 * Filename: skeleton_form.php -> report1.php
 * 
 * Purpose: processes data forms through action modes: add, edit, delete
 *          for dealtracker software 
 *                                                             
 * Created: 9/1/11   
 * 
 * Last Change: 
 *               12.22.11  - added 'closing task' features
 *  
 * Author:   Toby Jones      
 *                                                                              
 ************************************************************************/   


 error_reporting(E_ALL);

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
  
  
  
  // custom variables section
// Ideally you can define your custom table and primary key here and the rest just works

$maintable="opportunities";

$primary_key="domain_name";

$display_str = null;
$ids_in_view ='';
  $email_list='';
if (strstr($_SESSION["access_groups"], 'linkbuilders-TeamV')){
     require 'db_connect_teamv.php';
 }else{
  require 'db_connect.php';
}


// initialize flags and variables;
$_SESSION["debug"]=0;
 
  
  
  
  ?> 
 <html>
 <head>
 <title>Dealtracker Records View</title>
  
   <?php                                                                         

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
  
 
  
  
  

//************************* ***********************************
//   BEGIN  action event form handler code   
//**********************************************************/
// The mode value SHOULD be a descriptive string of what action (button click)
// we are doing.

if (isset($_POST["mode"]) || isset($_GET["mode"])){
     
     
     
     
      if ($_POST["mode"]=="range_save_status"){
       
      
    	$id_list=explode(',',$_POST["id_range"]);
    	$update_id_arr = null;
       
    	 foreach( $id_list as $id_item){
          
          if (stripos($id_item,'-') !== false){
            $ends=( explode('-',$id_item) );
            $tempstr='';
            for($i=$ends[0]; $i<=$ends[1]; $i++){
              $update_id_arr []=   $i;
            }//end inner for 
          }else{  //end if  // this is a single
       
            $update_id_arr []=  $id_item;
          
          }
       } //end foreach
        $qs="";
       foreach  ($update_id_arr as $update_id){
       
                 //permission check    
                 perm_check_opp($update_id);
                
        
               $qs =  "update opportunities set deal_status='".$_POST["batch_status"]."' where opp_id=$update_id;";
                $result=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
    
                $display_str .= "<br>$qs";
                  show_results(); 
       } //end foreach;
       
      // echo "<br>mode=range_save qs=".$qs; 
      //  $result=mysql_query($qs);
        
       $display_str .=  "<p>&nbsp;</p>Above actions are completed.";
       
    } // end if mode  rang_save_status
    
    
    
    
    
    
    
    
    if ($_POST["mode"]=="range_save_approved"){
       
      
    	$id_list=explode(',',$_POST["id_range"]);
    	$update_id_arr = null;
       
    	 foreach( $id_list as $id_item){
          
          if (stripos($id_item,'-') !== false){
            $ends=( explode('-',$id_item) );
            $tempstr='';
            for($i=$ends[0]; $i<=$ends[1]; $i++){
              $update_id_arr []=   $i;
            }//end inner for 
          }else{  //end if  // this is a single
       
            $update_id_arr []=  $id_item;
          
          }
       } //end foreach
        $qs="";
       foreach  ($update_id_arr as $update_id){
       
                //permission check
                perm_check_opp($update_id);
                  
               $qs =  "update opportunities set approved='".$_POST["batch_approved"]."' where opp_id=$update_id;";
                $result=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
    
                $display_str .=  "<br>$qs";
       
       } //end foreach;
       
      // echo "<br>mode=range_save qs=".$qs; 
      //  $result=mysql_query($qs);
        
        $display_str .=  "<p>&nbsp;</p>Above actions are completed.";
       
        show_results();
       
    } // end if mode  rang_save_approved
    
    
    
    
    
      if ($_POST["mode"]=="range_save_actions"){
       
      
    	$id_list=explode(',',$_POST["id_range"]);
    	$update_id_arr = null;
       
    	 foreach( $id_list as $id_item){
          
          if (stripos($id_item,'-') !== false){
            $ends=( explode('-',$id_item) );
            $tempstr='';
            for($i=$ends[0]; $i<=$ends[1]; $i++){
              $update_id_arr []=   $i;
            }//end inner for 
          }else{  //end if  // this is a single
       
            $update_id_arr []=  $id_item;
          
          }
       } //end foreach
        $qs="";
       foreach  ($update_id_arr as $update_id){
       
                 //permission check
                perm_check_opp($update_id);
       
               $qs =  "update opportunities set actions_status='".$_POST["batch_actions_status"]."' where opp_id=$update_id;";
                $result=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
    
                $display_str .=  "<br>$qs";
       
       } //end foreach;
       
      // echo "<br>mode=range_save qs=".$qs; 
      //  $result=mysql_query($qs);
        
        $display_str .=  "<p>&nbsp;</p>Above actions are completed.";
         show_results(); 
    } // end if mode  rang_save_actions
    
    
    
    
      if ($_POST["mode"]=="range_save_closing_task"){
       
      
    	$id_list=explode(',',$_POST["id_range"]);
    	$update_id_arr = null;
       
    	 foreach( $id_list as $id_item){
          
          if (stripos($id_item,'-') !== false){
            $ends=( explode('-',$id_item) );
            $tempstr='';
            for($i=$ends[0]; $i<=$ends[1]; $i++){
              $update_id_arr []=   $i;
            }//end inner for 
          }else{  //end if  // this is a single
       
            $update_id_arr []=  $id_item;
          
          }
       } //end foreach
        $qs="";
       foreach  ($update_id_arr as $update_id){
       
               perm_check_opp($update_id);
               
               $qs =  "update opportunities set closing_task='".$_POST["batch_actions_closing_task"]."' where opp_id=$update_id;";
                $result=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
    
                $display_str .=  "<br>$qs";
       
       } //end foreach;
       
      // echo "<br>mode=range_save qs=".$qs; 
      //  $result=mysql_query($qs);
        
        $display_str .=  "<p>&nbsp;</p>Above actions are completed.";
         show_results(); 
    } // end if mode  rang_save_actions
    
    
    
    
    
    //****** filter ***********//                                               
    if ($_POST["mode"]=="filter"){
       
      
    	 show_results(); //reload the new data
    	 
    	 //refesh existin view
    	 
    //	 echo '<script lang="javascript"> parent.parent.main.location.href="'.$_SERVER["PHP_SELF"].'";';
    //	 echo "</script>";
    	
    }
    
    if ($_POST["mode"]=="recent"){
       
      
    	 init(); //reload the new data
    	 
    	 
    }
    
   
}  else {//end if isset(mode)

          init();
       

} //end else Mode not set
// END action event handler


//************************* ***********************************
//  function: perm_check_opp($oppid)
//  
//     
//**********************************************************/


function perm_check_opp($oppid){
      
     //include 'db_connect.php';
     global $db_object;
   
     
     global $display_str;
      // load an initialized address form for editing
	   $qs="SELECT domain_name from opportunities WHERE ";
               $qs.="opp_id=".$oppid;
               
               if( stripos($_SESSION["memb"],'linkbuilder_admin') === false){
               $qs .= " AND linkbuilder_id=".$_SESSION["loggedin_linkbuilder_id"];
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
	           
	                 echo("<p>&nbsp;</p><center><h3>Be careful! You just tried to edit Opp id=".$oppid);
                   echo (", and that opportunity is not assigned to you.<br /> ");
                   echo ('<a href="report1.php">Go back</a> and change this entry.</h3>');
                   die  ('<a href="mailto:tjones@kimongroup.com">Contact</a> Dealtracker support if you think this is an error.</center>');
	           }
	         
	 
	 
}// end function init()


//************************* ***********************************
//  function: init()
//  
//     
//**********************************************************/


function init(){
      
     //include 'db_connect.php';
     global $db_object;
     global $maintable;
     global $primary_key,$domain;
     
     global $display_str;
      // load an initialized address form for editing
	  $qs="select * from opportunities, linkbuilders Where linkbuilders.linkbuilder_id=opportunities.linkbuilder_id order by opp_id DESC limit 300";
	  
	  
	  $display_str.= "<hr><h3> Recently Added Records</h3>";
	  
    fetch_data($qs);
	 
	 
}// end function init()



//************************* ***********************************
//  function: create()
//  inserts an empty record into addr_forms to generate a form_id to use in save()
//     
//**********************************************************/
function create(){
      
       global $db_object;
       global $maintable;
       global $primary_key,$domain;
      
    //insert a new empty record just to get a new form_id 
     $qs="INSERT INTO ".$maintable."(opp_id,domain_name) values(0,'".$domain."');";
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
    global $primary_key,$domain;
    
    
    //echo "<br> insave() domain=".$domain;
    
   $qs = "UPDATE ".$maintable." SET ";
  // $qs=$qs." doc_group_id=".$_SESSION["doc_group_id"].",";
   
   //print_r($_POST);
   
    foreach($_POST as $key=>$value){
		if (strlen($value) > 0 && $key != "mode" && $key != $primary_key && $key != "last_action" && $key != "batch_status" ){
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
  

  // require 'show_results.php';
 
  //************************* ***********************************
//  function: show_results()
//  loads a form record into a session var to be used to preLoad the form data
//     
//**********************************************************/
function show_results() {
      //include 'db_connect.php';
     global $db_object;
     global $maintable;
     global $primary_key,$domain;
      global $display_str; 
      $display_str=null; //reset
      // load an initialized address form for editing
	  $qs="select * from ".$maintable.",linkbuilders  WHERE ";
	  
	  
	  $trimflag=0;
	  
	   foreach($_POST as $key=>$value){
			 //input exclusions for this mode go here
			if (strlen($value) > 0){
			   if ($key != "mode" && $key != "last_action_start" && $key != "last_action_end" 
         && $key != "id_range" && $key != "linkbuilder_id"&& $key != "batch_status"
          && $key != "batch_approved" && $key != "batch_actions_status" 
          && $key != "batch_actions_closing_task" && $key != "batch_template"
           && $key != "template_approved" && $key != "message_body" 
            && $key != "message_subject") {
			   

              
					
				// append this $_POST to querystring 
					 $value=trim($value);
					 $qs=$qs."$key like '$value%' AND "; 
					 $trimflag=1;
			   } //end if !mode
			   
			   if ( $key == "linkbuilder_id" ) {
			        
					   if ($value == -1){ // wildcard all linkbuilders
					   
    					   $qs=$qs." opportunities.linkbuilder_id > 0 AND "; //selecting all
    					   $trimflag=1;
					   }else{
				     
    					 $value=trim($value);
    					 $qs=$qs." opportunities.linkbuilder_id = '$value' AND "; 
    					 $trimflag=1;
					   }//end else
			   } //end if linkbuilder_id
			   
			   
			  }// end if
	    }   //end foreach
	  
	  
	  // clip trailing comma at end of list
	  if($trimflag==1){
	  
    $qs=substr($qs,0,(strlen($qs)-4)); 
   // echo "<br>trimmed qs to :  $qs<br>";
   	}
	  
	  
	  if ( strlen( $_POST["last_action_start"] ) > 4  && strlen( $_POST["last_action_end"] ) > 4 ){
			  // if(trimflag==1){
					$qs .= " AND ";
			//	} 
				 
			   $qs .= " last_action >= '".$_POST["last_action_start"]."' AND last_action <= '".$_POST["last_action_end"]."' "; 
		}	   
	  
	  
	  $qs .= " AND linkbuilders.linkbuilder_id=opportunities.linkbuilder_id ";
    
    $qs .= " order by deal_status, actions_status, last_action desc";
     
  //$display_str .=  "<p>query: ".$qs;    
    
	 $display_str .= "<hr><h3> Search Results</h3>";
     fetch_data($qs);
	 
	 
	 
  } //end function
  
  
  
   
    //************************* ***********************************
//  function: fetch_data()
//  does trhe given query and render results
//     
//**********************************************************/
function fetch_data( $qs) {
      //include 'db_connect.php';
     global $db_object;
     global $maintable;
     global $primary_key,$domain;
  
     global $display_str;
  
     global $ids_in_view;
      global $email_list;
  
    $list_set=mysql_query($qs);
      
		  if (mysql_error()){
    	   echo("DB error in edit() $qs");
      }  
	  
      if ( mysql_affected_rows()==0 ){
         echo("<p>$primary_key not in records:".$_SESSION[$primary_key]);
      }
      
      // form_rec to be used by preLoad()
      //$form_rec=$form_set->fetchrow();
	 
	   $display_str .= '<table  class="datagrid" border="1" cellpadding="4" cellspacing="0" width="1000">';
	  $display_str .= '<tr><th class="closing">Closing Task</th><th class="opp">ID</th><th class="opp">Domain</th><th class="opp">Assigned to</th><th class="opp">Status</th>';
	  
	    // commen tbelow has "copy email list" button
	    // $display_str .= '<th class="opp">Approved</th><th class="opp">Email Addr.<a  href="javascript:doShowEmail()" >[Copy]</a> </th><th class="opp">Actions</th><th class="opp">Notes</th><th class="opp">Last Action</th><th class="opp">Finder</th>';
       
       $display_str .= '<th class="opp">Approved</th><th class="opp">Email Addr. </th><th class="opp">Actions</th><th class="opp">Notes</th><th class="opp">Last Action</th><th class="opp">Finder</th>';
       
       $display_str .= '<th class="deal">Posting Date</th><th class="deal">Keywords</th><th class="deal">Keyword Targets</th>';
       $display_str .= '<th class="deal">Article Loc.</th><th class="deal">Compnts.</th><th class="deal">Dur.</th><th class="deal">Value</th>';
	     $display_str .= '<th class="deal">Cost</th>';
       $display_str .= '<th class="deal">Paypal Email</th><th class="deal">Deal Date</th></tr>';
    
    
      $ids_in_view = '"';
      while ($list_rec = mysql_fetch_array($list_set, MYSQL_ASSOC) ){
	  
	         //list keeping
	         $ids_in_view .= ''.$list_rec["opp_id"].',';
	         $email_list .= trim($list_rec["special_codes"]).';';
	        
	        
	        
	       $display_str .= '<tr><td>&nbsp;'.$list_rec["closing_task"].'</td><td>'.$list_rec["opp_id"].'</td><td><a href="http://www.'.$list_rec["domain_name"].'">'.$list_rec["domain_name"]."</a></td><td>";
		 //  $display_str .= $list_rec["linkbuilder_id"]."</td><td>".$list_rec["deal_status"];
		  $display_str .= $list_rec["name_first"]." ".$list_rec["name_last"]."</td><td>".$list_rec["deal_status"]."</td><td>".$list_rec["approved"];
		  
		    $display_str .= "</td><td>&nbsp;".$list_rec["special_codes"]."</td><td>&nbsp;".$list_rec["actions_status"]."</td><td>&nbsp;".$list_rec["notes"];
		  
		   $display_str .=    "</td><td>".$list_rec["last_action"]. "</td><td>&nbsp;".$list_rec["finder"];
		   
       /// end opps data start deal data
		    
		     $display_str .= "</td><td>&nbsp;".$list_rec["posting_date"];
		     $display_str .= "</td><td>&nbsp;".$list_rec["keywords"];
		      $display_str .= "</td><td>&nbsp;".$list_rec["keyword_targets"];
		      $display_str .= "</td><td>&nbsp;".$list_rec["article_location"];
		      $display_str .= "</td><td>&nbsp;".$list_rec["components"];
		      
		   $display_str .= "</td><td>&nbsp;".$list_rec["duration"]."</td><td>&nbsp;".$list_rec["deal_value"];
		  
		   $display_str .= "</td><td>&nbsp;$".$list_rec["deal_cost"];
		  
       $display_str .= "</td><td>&nbsp;".$list_rec["paypal_email"]."</td><td>&nbsp;".$list_rec["deal_date"];
      
       
		   
		   
		  
		   
		   $display_str .= "</td><td></tr>"; 
	       
	  
	  } //end while
	  
     $display_str .= "</table>";
     
     // format the ids-in_view string
     
     $ids_in_view= rtrim($ids_in_view, ',').'"';
     
     
     // get this into javascript that can be used in batch tools above 
     
    
  } //end function fetch data
 
  
  
  
  
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
 


 
 
 
 
 <script lang="javascript">
 
/********** action event handlers *****************/

   

 function doAllInView() {
 
     var ids_in_view = <?php echo $ids_in_view;?>; 
 
      //alert ( "ids_in_view="+ids_in_view);
      
      ids_arr = ids_in_view.split(',');
      
      if (ids_arr.length > 15){
      
            if (confirm("Are you sure you want to select all "+ids_arr.length+" records currently in view?") ) {
                   document.forms.mainform.id_range.value= ids_in_view;
            
            } //end inner if
         
      } else{    // en dif arr_length
      
            // this is a short list
           document.forms.mainform.id_range.value= ids_in_view;
      
       } //end else
      
      // alert("No other action or page loads happens here - WTF");
      // ANSWER: Any button inside the form canautomatically submit the form 
 } // end function



 function doRangeSaveStatus(){
    // validate all
 	
 	
 	 if(confirm("Are you sure you want to save changes to 'DEAL STATUS' for rows having IDs in the given range?"   )){
   	document.forms.mainform.mode.value="range_save_status";
  	document.forms.mainform.submit();
 	}//end if confirm
	//document.location.href='report1.php';
 }


 function doRangeSaveApproved(){
    // validate all
 	
 	
 	 if(confirm("Are you sure you want to save changes to 'APPROVED' for rows having IDs in the given range?"   )){
   	document.forms.mainform.mode.value="range_save_approved";
  	document.forms.mainform.submit();
 	}//end if confirm
	//document.location.href='report1.php';
 }


  function doRangeSaveActions(){
    // validate all
 	
 	
 	 if(confirm("Are you sure you want to save changes to 'ACTIONS STATUS' for rows having IDs in the given range?"   )){
   	document.forms.mainform.mode.value="range_save_actions";
  	document.forms.mainform.submit();
 	}//end if confirm
	//document.location.href='report1.php';
 }


 function doRangeSaveClosingTask(){
    // validate all
 	
 	
 	 if(confirm("Are you sure you want to save changes to 'Closing Task' for rows having IDs in the given range?"   )){
   	document.forms.mainform.mode.value="range_save_closing_task";
  	document.forms.mainform.submit();
 	}//end if confirm
	//document.location.href='report1.php';
 }



 function doRecent(){
    // validate all
 	document.forms.mainform.mode.value="recent";
 	document.forms.mainform.submit();
	//document.location.href='report1.php';
 }
 
 function doFilter(){
    // validate all
 	document.forms.mainform.mode.value="filter";
 	document.forms.mainform.submit();
 }
 
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
 
 function doCloseEmail(){
 
 document.getElementById('emaildiv').style.visibility= 'hidden';
 
 }
 
  function doShowEmail(){
 
   document.getElementById('emaildiv').style.visibility= 'visible';
 
 }
 /***********  VALIDATION ********************/
// use  maskKey functions at /common/functions/mask_functions.js
 
 </script>
 
 
  
<script src="calendarDateInput.js"></script>



<style>

  .closing{
   background-color: rgb(255,208,128);
   width: 100px;
 }
 .opp{
   background-color: rgb(204,255,255);
   width: 100px;
 }
 .deal{
   background-color: rgb(204,255,153);
   width: 100px;
 }
 .datagrid {
   width:1000px;
 }
 .datagrid td {
   width:100px;
 }
 
 
 .emaildiv{
 position:absolute;
   border:1px rgb(0,102,0) solid ;
  visibility: hidden;
  padding: 5px 5px 5px 5px;
  display: inline;
  background-color: rgb(255,255,204);
 }
 
</style>


  </head>
 
<body>


<p>


<center>
<table><tr>
<td><img src="img/grid.png" height="100" width="100"/>
</td><td>
<h2>DealTracker Records View</h2>


<?php echo "User: ".$_SESSION["peast_username"]; ?>
 <?php
 require 'menu.php';
 ?> 
 
</td>
 </tr></table>
 
</center>



<div id="emaildiv" class="emaildiv">
      <center><strong>Copy Email List</strong></center<br>
     <textarea cols="80" rows="10"><?php echo $email_list; ?></textarea>
     <button onclick="doCloseEmail()">Close</button>
</div>

<form name="mainform" method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
<input type="hidden" name="mode">

<table id="outer" border="1" cellspacing="0" width="90%"> <tr> <td width="30%" valign="top">

 <?php require 'search_filter_gui.php'; ?>
		
</td>







<td valign="top" width="30%">
  <h4>2) Batch Tools - Set Range<h4>
 
 
  <table>  
 <tr>
  <td align="left" valign="top">ID Range:</td><td valign="top"> <input name="id_range" type="text" size="40">
     <p>&nbsp;</p> </td>
  </tr>
  <tr>
  <td align="left" colspan="2">*Use commas and dashes for range. ex: 3-8,11,13 
    </td>
  </tr>
  <tr>
  <td align="left" colspan="2"> Or you can 
       <a  href="javascript:doAllInView()"> Select All IDs in Current Page View </a>
    </td>
  </tr>
  
   <tr>
  <td align="right" colspan="2"><p>&nbsp;</p> <strong>Then</strong> select a batch operation from the right ->   
    </td>
  </tr>
  </table>    



</td>

<td valign="top" width="30%" align="left">



 
  <h4>3) Batch Tools - Save Changes<h4>
 <table>  
 <tr><td align="left">Status:</td><td>
      <select name="batch_status"> 
      
		<?php
	   require 'deal_status_options.php';
	?>
	
  </select></td>  <td>  <button  onclick="doRangeSaveStatus()" >Batch Save</button>   </td>
   </tr>

 </table>
 
  <p>&nbsp;</p>
  
 <table>
 <tr><td align="left">Approved:</td><td>
      <select name="batch_approved"> 
      <option value="Yes" >Yes</option>
	    <option value="No" >No</option>
      </select>
  
  
  </td> <td><button  onclick="doRangeSaveApproved()" >Batch Save</button> </td>
   </tr>
  
 
 </table>
 
  
  
   <p>&nbsp;</p>
  
 <table>
 <tr><td align="left">Actions Status:</td><td>
      <select name="batch_actions_status"> 
        
		<?php
	   require 'actions_status_options.php';
	?>
      </select>
  
  
  </td> <td><button  onclick="doRangeSaveActions()" >Batch Save</button> </td>
   </tr>
  
 
 </table>
  
  
  
  
   <p>&nbsp;</p>
  
 <table>
 <tr><td align="left">Closing Task:</td><td>
      <select name="batch_actions_closing_task"> 
        
		<?php
	   require 'closing_task_options.php';
	?>
      </select>
  
  
  </td> <td><button  onclick="doRangeSaveClosingTask()" >Batch Save</button> </td>
   </tr>
  
 
 </table>
  
  
  
  
</td>

</tr>	</table>  <!-- end outer table container -->

	

<!--<button onclick="doCreate()">Create New Field</button>-->


 
 
 
 
 
<?

echo $display_str; // this should have the report view data grid


unset($_SESSION["form_rec"]);  // release preloads
unset($_SESSION["loggedin_linkbuilder_id"]);  // release old linkbuilder_ids if any
?> 

    <center> 
<br><a href="report1.php">Records View</a> |   <a href="mail_control.php">Mail Control</a>
 </center>
