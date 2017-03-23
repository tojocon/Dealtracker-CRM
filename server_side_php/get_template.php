<?php
 /*                                                                             
 ****************************************************************************                                                                                
 * Filename: get_template.php
 * 
 * Purpose:  loads an email template into view
                                                                   
 *                                                             
 * Created:  7/31/12    
 *  
 * Author:   Toby Jones    
 *  
 * updated: 
 *          
 *    
 * Notes:                                                                                

************************************************************************/  


ini_set("error_reporting", E_ERROR);






 //get the q parameter from URL
$q=$_GET["q"];

 //make db connection
 require 'db_connect.php';



$clean_q = mysql_real_escape_string(trim($q)); 

// echo "<br> clean_q =$clean_q ";


//if ( is_integer($clean_q) ){

        $qs="SELECT message_body,message_subject from mail_templates where template_id = $clean_q";
         
       // echo "<br> qs=$qs";
         
         $set=mysql_query($qs);
    
         $rec = mysql_fetch_array($set, MYSQL_ASSOC);
          
         echo  stripslashes($rec["message_subject"]).'|'.stripslashes($rec["message_body"]);
//} //end is paramter is valid int



    
