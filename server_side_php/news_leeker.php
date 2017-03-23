<?php
/*                                                                             
 ****************************************************************************                                                                                
 * Filename: news_leaker.php
 *      
 * Purpose:  this script reads from a mail queue in database and leaks out
 *            newsletters in a randomized natural way as if sent by a human.
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
 * Notes:                                                                                         
************************************************************************/ 


 $max_interval=26; //secs
 $message_arr=null;

 $email_from='steve.jobs@linux.org';

 $message_body='Thank you Dennis Ritchie and Richard Stallman for building the foundation of good software.';

//check to see if script is running already


$target_arr[0]='toby@tojocon.com';
$target_arr[1]='toby.jones@primemediaconsulting.com';
$target_arr[2]='toby@inkpal.com';


$fh=fopen('newsletter_'.date("Y.m.d-h.i").'log','w');


fwrite($fh,"\nentering main loop\n");
$waiting_count =count($target_arr)-1;

 $headers='FROM: steve.jobs@gnu.org';

while ($waiting_count >= 0){


    fwrite($fh,"\ntarget=".$target_arr[$waiting_count]."\n");
  
    $sleep_time=rand(1, $max_interval);
    
    echo  "\nsleep_time=".$sleep_time;
    //sleep($sleep_time);  
    
    $send_accepted=false;
    $send_accepted= mail($target_arr[$waiting_count],'Ink Newsletter Test',$message_body,$headers);         
          
          
     //// update sent =1
     if ( $send_accepted){
             fwrite($fh,"\nsend succeeded\n");
     } else{
           // sent=2 is error flag
           
          $entry="mail() failed using to:$email_to, from:$email_from body:".$message_body;
          fwrite($fh,$entry."\n"); 
        
    
     }
  
       
  
    $waiting_count--;
  
  
} //end while $waiting_count > 0


fclose($fh);

echo "\nDone\n";

/*
//function send_message(){

    // mail(string to, string subject, string message)
}

*/


?>
