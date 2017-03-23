<?php
/*                                                                             
 ****************************************************************************                                                                                
 * Filename: mail_leaker.php
 *      
 * Purpose:  this script reads from a mail queue in database and leaks out
 *            messages in a randomized natural way as if sent by a human.
 *            
 *                                                                 
 *                                                             
 * Created:  5/22/12   
 *  
 * Author:   Toby Jones    
 *  
 * updated:  
 *          
 *    
 * Notes:   3) Will this run as a service loop always on 
 *          OR 
 *          2) will this be cron jobs?  
 *          OR
 *          1)kicks off with user action, checks for existing process                                                                                      
************************************************************************/ 


 $max_interval=250; //secs
 $message_arr=null;

//check to see if script is running already


//db connect 
require 'db_connect.php';


// select from out_queue where sent=0
$qs="SELECT * FROM mail_queue where sent=0;";
$set=mysql_query($qs);
  
 while($row = mysql_fetch_array($set, MYSQL_ASSOC) ){
            
            $message_arr []= $row; 
  } //end while
 
 $set=null;

$waiting_count =count($message_arr);


while ($waiting_count > 0){


  
    $sleep_time=rand(1, $max_interval);
    
    echo  "\nsleep_time=".$sleep_time
    //sleep($sleep_time);  
    
    $send_accepted=false;
    $send_accepted= mail($email_to,$email_from,$rec["message_body"]);
  
                 mail(string to, string subject, string message, [string additional_headers], [string additional_parameters])
  
     //// update sent =1
     if ( $send_accepted){
        $qs='Update mail_queue set sent=1 where message_id='.$message_id=.';';
     } else{
           // sent=2 is error flag
          $qs='Update mail_queue set sent=2 where message_id='.$message_id=.';';
    
          $entry="mail() failed using to:$email_to, from:$email_from body:".$rec["message_body"];
          
          $qs="Insert INTO mail_log(message_id,entry)='".."' where message_id=".$message_id=.';';
    
     }
  
       
  
    $waiting_count--;
    
     //check for new entries if we are almost done:
    if ($waiting_count == 1){
        // select from out_queue where sent=0
        $qs="SELECT * FROM mail_queue where sent=0;";
        $set=mysql_query($qs);
          
         while($row = mysql_fetch_array($set, MYSQL_ASSOC) ){
                    
                    $message_arr []= $row; 
          } //end while
         
         $set=null;
    } //end if new entries 
  
  
} //end while $waiting_count > 0


/*
//function send_message(){

    // mail(string to, string subject, string message)
}

*/


?>
