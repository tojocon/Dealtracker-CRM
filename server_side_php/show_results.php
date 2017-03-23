<?php
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
     
  $display_str .=  "<p>query: ".$qs;    
    
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
?>