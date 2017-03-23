<?php
/*                                                                             
 ****************************************************************************                                                                                
 * Filename: mail_leaker_manual.php
 *      
 * Purpose:  runs once to test message sending like real leaker script 
 *           this script reads from a mail queue in database and leaks out
 *            messages in a randomized natural way as if sent by a human.
 *            
 *                                                                 
 *                                                             
 * Created:  6/14/12   
 *  
 * Author:   Toby Jones    
 *  
 * updated:  
 *          
 *    
 * Notes:   Always kickoff in background with '&' 
 *            php-cli mail_leaker.php &
 *            And make sure isn't runing alraedy with
 *             ps -ef | grep leaker  
 *           and clear lock file in gen/                                                                                     
************************************************************************/ 


 $max_interval=8; //secs : low for testing high for live


   


 
 
  
 $fh=fopen('gen/mleak_'.date("ymd").'.log','a');
  
  
 $message_arr=null;




 require 'db_connect.php';



 $main_qs="SELECT q.*,b.alias_email,b.signature FROM mail_queue q,linkbuilders b where sent_status=0";
 $main_qs.=" AND q.linkbuilder_id=b.linkbuilder_id";


 $mail_set=mysql_query($main_qs);
      
		  if (mysql_error()){
    	  // echo("DB error in edit() $qs");
    	    fwrite($fh,"DB error in edit() $main_qs");
      }  
	  
      if ( mysql_affected_rows()==0 ){
         // echo("<p>No messages waiting.</p>");
      }
      
  
      while ($list_rec = mysql_fetch_array($mail_set, MYSQL_ASSOC) ){
             $target_arr []= $list_rec;
      }


  





//fwrite($fh,"\nentering main loop\n");
$waiting_count =count($target_arr)-1;

 

while ($waiting_count >= 0){
// while (true){    //infinite loop - be careful
    
   // $headers='FROM: steve.jobs@gnu.org';
      
    
    //fwrite($fh,"\ntarget=".$target_arr[$waiting_count]."\n");
    // flush();
     
  $sleep_time=rand(1, $max_interval);
    
  //echo  "\nsleep_time=".$sleep_time;
   // fwrite($fh,"\nsleep_time=".$sleep_time;);
   // flush();
   sleep($sleep_time);  
  
  
  
   if (strlen($target_arr[$waiting_count]["message_to"]) > 3){
  
      
       $headers='FROM: '.$target_arr[$waiting_count]["alias_email"]."\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
       
      
    $send_accepted=false;
   
   
    $body_and_sig = $target_arr[$waiting_count]["message_body"];
    
    if ( strlen($target_arr[$waiting_count]["signature"])  > 0 ){
       $body_and_sig .= $target_arr[$waiting_count]["signature"];
     }

      
      //5th param used for envelop sender cmd option -f
     $fifth_param=' -f '.trim($target_arr[$waiting_count]["alias_email"]); 

 /********************************************************************
  *  Main Mail SEND
  *  
  * !! remove  if condition for live mode !!     
  *
  *
  *
  ********************************************************************/         
   
   if ( stripos($target_arr[$waiting_count]["message_to"], '@inkpal.com') ||
        stripos($target_arr[$waiting_count]["message_to"], '@kimongroup.com') ||
        stripos($target_arr[$waiting_count]["message_to"], '@tojocon.com')
         stripos($target_arr[$waiting_count]["message_to"], 'seo4hire@aol.com')
         stripos($target_arr[$waiting_count]["message_to"], 'consultant_toby@hotmail.com')
        ){
        
       
        
            /*$send_accepted= mail($target_arr[$waiting_count]["message_to"],
                            $target_arr[$waiting_count]["message_subject"],
                            $body_and_sig,$headers);   */      
      
             //5th Parameter
             
              $send_accepted= mail(trim($target_arr[$waiting_count]["message_to"]),
                            $target_arr[$waiting_count]["message_subject"],
                            $body_and_sig,$headers,$fifth_param);  
        
        
      
    } // end if office team only testin   
          
     //// update sent =1
     if ( $send_accepted){
            // fwrite($fh,"\nsend succeeded\n");
             $qs="Update mail_queue set sent_status=1 where message_id=".$target_arr[$waiting_count]["message_id"];
              
             
     } else{
           // sent=2 is error flag
           
          $entry="mail() failed using to:".$target_arr[$waiting_count]["message_to"];
          $entry .="; from:".$target_arr[$waiting_count]["alias_email"]."; body:".$body_and_sig;
            //fwrite($fh,$entry."\n"); 
           // flush();
          $qs="INSERT INTO mail_log(event_id,message_id,entry) VALUES(0,".$target_arr[$waiting_count]["message_id"].",'".$entry."')";
          
           $result=mysql_query($qs);
           
            if (mysql_error()  ){
    	             //echo ("DB error in $qs".mysql_error());
            }
            
         
          $qs="Update mail_queue set sent_status=2 where message_id=".$target_arr[$waiting_count]["message_id"];
    
     } //end else
  
           // echo "<br>qs=".$qs;
           $result=mysql_query($qs);
                	
} //end if length check 
       
  
         //fwrite($fh,"\nwaiting count ".$waiting_count);
         //flush();
  
          // see if we have new messages in the queue
        
  
               
           $waiting_count--;
           if ($waiting_count<0){
             $waiting_count= -1;
           }
    
} //end while $waiting_count > 0


fclose($fh);
 

  
//echo "\nDone 9\n";
   
/*
//function send_message(){

    // mail(string to, string subject, string message)
}

*/


?>
