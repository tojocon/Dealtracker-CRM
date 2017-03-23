<?php 
/*                                                                              
 ****************************************************************************                                                                                
 * Filename: mail_control.php
 * 
 * Purpose: Sets up email que with batch mails. Processes data forms through action modes: add, edit, delete
 *          for dealtracker software 
 *                                                             
 * Created: 7/30/12                                                                        -
 *  Author:   Toby Jones  
 *  
 *  
 * Last Change: 
 *              
 *  
 *    
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
  
  
  

  
  // custom variables section
// Ideally you can define your custom table and primary key here and the rest just works

$maintable="opportunities";

$primary_key="domain_name";

$show_tools=true;

$display_str = null;
$ids_in_view ='';
  $email_list='';
/* if (strstr($_SESSION["access_groups"], 'linkbuilders-TeamV')){
     require 'db_connect_teamv.php';
 }else{
  require 'db_connect.php';
}
*/

switch( $_SESSION["access_groups"]){

   case  'linkbuilders-TeamV':
        require 'db_connect_teamv.php';
        break;

    case  'linkbuilders-Bluestripe':
        require 'db_connect_bluestripe.php';
        break;
        
    default:
         require 'db_connect.php';
} //switch




// initialize flags and variables;
$_SESSION["debug"]=0;




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
     
     
     
        //  echo '<br>$_POST["mode"]='.$_POST["mode"];
        //  echo '<br>$_GET["mode"]='.$_GET["mode"];
        
        
       /***************************************************************
        *********************************
        *  BATCH_QUEUE
        *  **********************************************************/
                               
       if ($_GET["mode"]=="batch_queue"){
       
             // echo '<br>inside batch_queue';
              //echo("mode=batch_queue<br>");
             	$id_list=explode(',',$_SESSION["id_range"]);
    	        $opp_id_arr = null;
       // echo("entering  foreach  (id_list <br>");   
    	 foreach( $id_list as $id_item){
          
          if (stripos($id_item,'-') !== false){
            $ends=( explode('-',$id_item) );
            $tempstr='';
            for($i=$ends[0]; $i<=$ends[1]; $i++){
              $opp_id_arr []=   $i;
            }//end inner for 
          }else{  //end if  // this is a single
       
            $opp_id_arr []=  $id_item;
          
          }
       } //end foreach
        $qs="";
        //$display_str .= "<br /><b>Please confirm these email targets:</b>";
       // $display_str .= '<table border="1"><tr><td valign="top">';
        $mesage_to='';
                $headers='';
        // echo("entering  foreach  (opp_id_arr <br>");        
       foreach  ($opp_id_arr as $opp_id){
       
               $qs =  "SELECT opp_id,domain_name,special_codes,alias_name,alias_email,article_location ";
               $qs .= "FROM opportunities p, linkbuilders b  where ";
               $qs .= "opp_id=$opp_id AND p.linkbuilder_id=b.linkbuilder_id ";
               $qs .= "AND p.linkbuilder_id=".$_SESSION["loggedin_linkbuilder_id"];
                
                $opp_set=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
    
    
                $opp_rec= mysql_fetch_array($opp_set, MYSQL_ASSOC);
                
               // $display_str .= "<br>".$opp_rec["domain_name"]." - ".$opp_rec["special_codes"];
                
                $mesage_to=$opp_rec["special_codes"];
                $headers="FROM: ".$opp_rec["alias_email"];
                
                  
                $body_subvars =subvars($opp_rec,$_SESSION["message_body"]);
                $subject_subvars =subvars($opp_rec,$_SESSION["message_subject"]);
                
               $qs= "INSERT INTO mail_queue(message_id,opp_id,";
               $qs.="message_subject,message_to,message_body,linkbuilder_id, sent_status) VALUES(0,$opp_id,'";
               $qs.=$subject_subvars."','".$opp_rec["special_codes"];
               $qs.="','".$body_subvars."',";
               $qs.=$_SESSION["loggedin_linkbuilder_id"].",0)";
               
               
                $result=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
               
                
               //$display_str .= "<br>".$qs;
                
                  
       } //end foreach;
                
       
             
           
             //echo("about to shell exec"); 
            // call mail_leaker as a background task
          //  shell_exec("/home3/inkpalne/public_html/dealtracker/launch_mailer.sh");
             
            // system ("/home3/inkpalne/public_html/dealtracker/launch_mailer.sh");
             
           //  `/home3/inkpalne/public_html/dealtracker/launch_mailer.sh`;
             
             // echo(" shell exec returned");   
            $display_str .="<p>&nbsp;</p><h3>Batch Send Email <b> Complete</h3>";
            
            $display_str .="<p>&nbsp;</p>* NOTE! MAKE SURE you change the Status, Actions Status, and Closing Task before moving to your next task.<p>&nbsp;</p>";
            
            
            
           $display_str .= "You just mailed to these Opps: ";
            $display_str .= '<input type="test" size="40" value="';
           $display_str .=$_SESSION["id_range"].'" readonly="readonly">';
            
             $_SESSION["id_range"]= null;
              $_SESSION["message_subject"]= null;
              $_SESSION["message_body"]= null;
            
            //$display_str .=" test mode only sends to *@inkpal.com or *@kimongroup.com<br /> or seo4hire@aol.com,consultant_toby@hotmail.com";
            
           //  $display_str .='<p>&nbsp;</p><a href="mail_control.php">Back to Mail Control</a><br />';
           //   $display_str .=' or <a href="report1.php">Records View</a><br />';
              
            //show_results(); 
              $show_tools=false; //hides top tool container
       }  //end mode batch_queue
     
     
     
     
     
     
        
        /***************************************************************
        *********************************
        *  CONFIRM_SEND
        *  **********************************************************/
       if ($_POST["mode"]=="confirm_send"){
       
              //remember to reset these $_SESSION vars after final save or cance;
              $_SESSION["id_range"]= $_POST["id_range"];
              $_SESSION["message_subject"]= trim($_POST["message_subject"]);
              $_SESSION["message_body"]= trim($_POST["message_body"]);
              
              
             	$id_list=explode(',',$_POST["id_range"]);
    	$opp_id_arr = null;
       
    	 foreach( $id_list as $id_item){
          
          if (stripos($id_item,'-') !== false){
            $ends=( explode('-',$id_item) );
            $tempstr='';
            for($i=$ends[0]; $i<=$ends[1]; $i++){
              $opp_id_arr []=   $i;
            }//end inner for 
          }else{  //end if  // this is a single
       
            $opp_id_arr []=  $id_item;
          
          }
       } //end foreach
        $qs="";
        $display_str .= '<br /><b><font color="red">Please confirm these email targets:</font></b>';
        $display_str .= '<table border="1"><tr><td valign="top">';
         $display_str .="Email Targets:<br />";
        $mesage_to='';
                $headers='';
             
        $owned_by_another_user=false;        
        
        $mail_max=20; // no more than 20 messages at once
        $opp_count=0; 
               
       foreach  ($opp_id_arr as $opp_id){
       
               $qs =  "SELECT opp_id,domain_name,special_codes,alias_name,alias_email ";
               $qs .= "FROM opportunities p, linkbuilders b  where ";
               $qs .= "opp_id=$opp_id AND p.linkbuilder_id=b.linkbuilder_id ";
               $qs .= "AND p.linkbuilder_id=".$_SESSION["loggedin_linkbuilder_id"];
                
                $opp_set=mysql_query($qs);
                	
		            if (mysql_error()  ){
    	             echo ("DB error in $qs".mysql_error());
                  }
    
    
                $opp_rec= mysql_fetch_array($opp_set, MYSQL_ASSOC);
                
                if (strlen($opp_rec["special_codes"]) > 3){
                
                $display_str .= "<br>".$opp_rec["domain_name"]." - ".$opp_rec["special_codes"];
                  } else {
                  
                     echo("<p>&nbsp;</p><center><h3>Either there is no email address for Opp id=".$opp_id);
                     echo (", or that opportunity is not assigned to you. ");
                     die ('<a href="mail_control.php">Go back</a> and change this entry.</h3></center>');
                  }
                $mesage_to=$opp_rec["special_codes"];
                $headers="FROM: ".$opp_rec["alias_email"];
                
                
                 
               // $display_str .= "<br>$qs";
              
              $opp_count++;
              
              if($opp_count > $mail_max){
                                         break;
              }
                  
       } //end foreach;
       
       
       
       $display_str .= '</td><td valign="top">';
       
            $display_str .="------------------Sample:----------------<br />".$headers;
            $display_str .="<br>To: ".$mesage_to;
            $display_str .="<br>Subject: ".stripslashes($_POST["message_subject"]);
            $display_str .="<br>Body:<br>".stripslashes($_POST["message_body"]);
            
     
            
             $display_str .= "</td></tr><tr><td colspan=2>";
             
           //  $display_str .='<p>&nbsp;</p><center><a href="javascript:doBatchQueue()"> Approve & Send</a><br /></center>'."</td></tr></table>";
           
              
              if($opp_count > $mail_max){
                     $display_str .='<p>&nbsp;</p><center> <b><font color="red">Too many targets selected.</font></b><br />You tried to send '.$opp_count.' messages, but the limit is set to '.$mail_max.'. Go back and select fewer targets to mail. </center><p>&nbsp;</p>';
              }else{
                  $display_str .='<p>&nbsp;</p><center><a href="mail_control.php?mode=batch_queue"> Approve & Send</a><br /></center>';
              }    
           $display_str .= "</td></tr></table>";
            //show_results(); 
            
            // $display_str .='<p>&nbsp;</p><center><div id="clickme" style="border: 1px blue solid;" onclick="wtf()"> Approve & Send</div></center>';
            
            $show_tools=false; //hides top tool container
            
       }  //end mode confirm_send
     
     
     
     
      
      
      
      
      
      
      
     
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
//  function: subvars($rec_arr,$body)
//  
//     
//**********************************************************/


function subvars($rec_arr,$body){

      $temp=$body;
      
      $rec_arr["domain_name"]=ucfirst($rec_arr["domain_name"]);
      
       $mark=strrpos($rec_arr["domain_name"], '.');
       $rec_arr["domain_name_base"]=substr( $rec_arr["domain_name"],0,$mark ); 
      
      foreach ($rec_arr as $key=>$value){
      
      
                $temp=str_ireplace("[$key]",$value, $temp);   
         
      } //end foreach

      return $temp;
} //end function subvars









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
     
    // $display_str .=  "<p>query: ".$qs;    
    
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
 
<html>
 <head>
 <title>Dealtracker Mail Control</title>
  
  
 <script type="text/javascript">
 
/********** action event handlers *****************/

  <?php
  //  Top Tools Container
  
  if ($show_tools){
  
?> 

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
 
 function loadTemplate(){
    
  
  	 
  	
  	if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp2=new XMLHttpRequest();
    }
  else
    {// code for IE6, IE5
    xmlhtt2p=new ActiveXObject("Microsoft.XMLHTTP");
    }
  
    
  xmlhttp2.onreadystatechange=function()
    {
    //alert( "readystate="+xmlhttp2.readyState+" && status="+xmlhttp2.status);
    
    if (xmlhttp2.readyState==4 && xmlhttp2.status==200 )
      { 
       // alert("ajax success!");
       //alert (xmlhttp2.responseText);
        var datastr= xmlhttp2.responseText;
        //alert("datastr="+datastr);
          var parts=datastr.split("|");
        document.getElementById("message_body").innerHTML=parts[1];
        document.forms.mainform.message_subject.value=parts[0];
         
      } else{
      
         //alert( "no go. ="+xmlhttp2.readyState+" && status="+xmlhttp2.status);
      }
    } //end anon function
    
    
     
    // alert(document.forms.mainform.batch_template);
  var theurl="get_template.php?q="+document.forms.mainform.batch_template.value;
  // var theurl="get_template.php?q=";
 // alert("theurl2 ="+theurl);
  xmlhttp2.open("GET",theurl,true);
  xmlhttp2.send();
 
 }
 
 
 
 function doRangeConfirmSend(){
 
 
      if (document.forms.mainform.id_range.value.length < 1){
      
            alert("no id range selected");
      }  else{
      
          document.forms.mainform.mode.value="confirm_send";
 	        document.forms.mainform.submit();
      } //end else
 } //end function
 
  
  
  
  
  <?php
  
    } //end if show_tools
  ?>
  
  
  

          
 function doBatchQueue() {
      alert("in  doBatchQueue 2");
     //document.forms.mainform.mode.value="batch_queue";
   
  	if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp3=new XMLHttpRequest();
    }
  else
    {// code for IE6, IE5
    xmlhttp3=new ActiveXObject("Microsoft.XMLHTTP");
    }
  
    
    
     
    // alert(document.forms.mainform.batch_template);
 var aurl ='mail_control.php?mode=batch_queue';
  // var theurl="get_template.php?q=";
 // alert("theurl2 ="+theurl);
  xmlhttp3.open("GET",aurl,true);
  xmlhttp3.send();
 
    
  
   
   alert("mail will be sent");
 
    window.location="http://inkpal.net/dealtracker/mail_control.php";
 	//   document.forms.mainform.submit();
    
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
<td><img src="img/mail_arrow.jpg" height="100" width="100"/>
</td><td>
<h2>DealTracker Mail Control</h2>

<?php echo "User: ".$_SESSION["peast_username"]; ?>

 <?php
 require 'menu.php';
 ?> 


 </td>
 </tr></table>
 
</center>




<form name="mainform" method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
<input type="hidden" name="mode">


<?php
  //  Top Tools Container
  
  if ($show_tools){
  
?>
<table id="outer" border="1" cellspacing="0" width="90%"> 
<tr> <td width="30%" valign="top">

  <?php require 'search_filter_gui.php'; ?>
		
</td>







<td valign="top" width="30%">
  <h4>2) Batch Email - Set Range<h4>
 
 
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



 
  <h4>3) Batch Email - Choose Template<h4>

 <table>  
 <tr><td align="left">Template:</td><td>
 <?php// echo "lb_id=".$_SESSION["loggedin_linkbuilder_id"]; ?>
      <select name="batch_template" onchange="loadTemplate()"> 
      <option value="0">choose a template</option>
		<?php
	   //require 'deal_status_options.php';
	   
	   $qs= "SELECT * from mail_templates where linkbuilder_id = ". $_SESSION["loggedin_linkbuilder_id"];
	   $qs .= "  ORDER by template_id";
	   
	   $t_set=mysql_query($qs);
		
		if(mysql_error()) {
		
			echo("error in select fill: ".mysql_error());
			//die($f_set->getMessage() );
		}
		
		
		
	
    
    //$actions_match_found=0;  
		while($t_rec= mysql_fetch_array($t_set, MYSQL_ASSOC)  ){
		    
		    if ($_SESSION["form_rec"]["actions_status"] == $t_rec["actions_status"]){
		        echo "<option value=".$t_rec["template_id"]." selected>".$t_rec["template_title"]."</option>\n";
		        
		    }else{
		        echo "<option value=".$t_rec["template_id"].">".$t_rec["template_title"]."</option>\n";
		    }
		    
		}//end while
	   
	?>
	
  </select>
  <script>
   loadTemplate();
   </script>
  
  <?php
    //	echo "<br/> form_rec][actions_status=".$_SESSION["form_rec"]["actions_status"] ;
   // echo "<br/> actions_status=".$t_rec["actions_status"] ;
  ?>
  &nbsp;&nbsp; <a href="manage_templates.php"  target="_blank">Manage Templates</a>
  
    
    </td>  
  
   </tr>
 	<tr><td>Subject:</td><td> <input name="message_subject"  <? preLoad("message_subject"); ?> size="50" readonly="readonly"> </td></tr>
	
  
 <tr>
 <td > 
      Body:</td><td>
     <textarea id="message_body" name="message_body" rows="6" cols="40" readonly="readonly"></textarea>
  
   </td>
   </tr>
  

   
  
  <tr>
  <td colspan="2" align="right"><button  onclick="doRangeConfirmSend()" >SEND Email Batch* </button>   </td>
   </tr>


 </table>
  * Messages will be queued and sent by Dealtracker soon
  <p>&nbsp;</p>
  
 

  
  
</td>

</tr>	</table>  <!-- end outer table container -->

<?php
 } //end if show_tools

?>	

<!--<button onclick="doCreate()">Create New Field</button>-->


 
 
 
 
<center> 
<?

//print_r($_SESSION);
 

echo $display_str; // this should have the report view data grid


unset($_SESSION["form_rec"]);  // release preloads
?> 

  </form>
  
  
<br><a href="report1.php">Records View</a> |   <a href="mail_control.php">Mail Control</a>
  </center>
