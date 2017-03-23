<?php
/*                                                                             
 ****************************************************************************                                                                                
 * Filename: mail_leaker.php
 *      
 * Purpose:  runs as an always true service (infinte loop with sleep). 
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


 //$max_interval=8; //secs : low for testing high for live

  $max_interval=180; // 172 s is 500 mails per day
   


  //use semaphore file exists for locking mechanism
 if ( file_exists("/home3/inkpalne/public_html/dealtracker/gen/mail_leak.loc")  ){
    // already running or had a crash before
    die("blocked from running with lockfile");
 }else{
 
   //make sure to delete lock file on exceptions -      
  $fh_lock =fopen("/home3/inkpalne/public_html/dealtracker/gen/mail_leak.loc","w");
  fwrite($fh_lock,"locked at ".date("Y-m-d"));
  flush();
  fclose($fh_lock);
 }
 
  
 $fh=fopen('/home3/inkpalne/public_html/dealtracker/gen/mleak_'.date("ymd").'.log','a');
  
  
 $message_arr=null;




 require '/home3/inkpalne/public_html/dealtracker/db_connect.php';



 $main_qs="SELECT q.*,b.alias_email,b.signature FROM mail_queue q,linkbuilders b where sent_status=0";
 $main_qs.=" AND q.linkbuilder_id=b.linkbuilder_id order by message_id desc";


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

 

//while ($waiting_count >= 0){
 while (true){    //infinite loop - be careful
    
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
      //$headers .= "MIME-Version: 1.0\r\n";
       $headers .= "Return-path: <".$target_arr[$waiting_count]["alias_email"].">\r\n";
        $headers .=  'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20120713 Thunderbird/14.0'."\r\n";
       
      // $bound = uniqid('-----------------------');
       
      // $bound='------------040300010804060002030502';
       
      // $headers .= 'Content-Type: multipart/alternative;'."\r\n".' boundary="'.$bound.'"'."\r\n\r\n";
       
      // $headers .= 'This is a multi-part message in MIME format.'."\r\n";
    //   $headers .= $bound."\r\n";
      $headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\r\n";
   $headers .= 'Content-Transfer-Encoding: 7bit'."\r\n";
      
       
      
    $send_accepted=false;
    $safety_rejected=0;
   
    $body_and_sig = $target_arr[$waiting_count]["message_body"];
    
    if ( strlen($target_arr[$waiting_count]["signature"])  > 0 ){
    
       
       $body_and_sig .= $target_arr[$waiting_count]["signature"];
     }

   

       $body_and_sig=stripslashes( $body_and_sig);
       
      // $text_only=str_ireplace('<br />', "\r\n", $body_and_sig);
       
      // $body_stripped=strip_tags($text_only);
                                
       $body_and_sig='<html><head><meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"></head>  <body bgcolor="#FFFFFF" text="#000000">'.$body_and_sig.' </body></html>';
       
       
       $strip_subject=  stripslashes( $target_arr[$waiting_count]["message_subject"]);
      
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
   
   
  /* if ( stripos($target_arr[$waiting_count]["message_to"], '@inkpal.com') ||
        stripos($target_arr[$waiting_count]["message_to"], '@kimongroup.com') ||
        stripos($target_arr[$waiting_count]["message_to"], '@tojocon.com') ||
          stripos($target_arr[$waiting_count]["message_to"], 'eo4hire@aol.com')||
         stripos($target_arr[$waiting_count]["message_to"], 'onsultant_toby@hotmail.com')
        ){
        
             
         */
            /*$send_accepted= mail($target_arr[$waiting_count]["message_to"],
                            $target_arr[$waiting_count]["message_subject"],
                            $body_and_sig,$headers);   */      
      
             //5th Parameter
             
            $send_accepted= mail(trim($target_arr[$waiting_count]["message_to"]),
                            $strip_subject,
                            $body_and_sig,$headers,$fifth_param);  
        
        
      
 /*   }else{ // end if office team only testin   
        $safety_rejected=1;
       }   
       */
     //// update sent =1
     if ( $send_accepted){
            // fwrite($fh,"\nsend succeeded\n");
             $qs="Update mail_queue set sent_status=1 where message_id=".$target_arr[$waiting_count]["message_id"];
              
             
     } else{
           // sent=2 is error flag
           
          $entry="mail() failed; saftey_rej=".$safety_rejected."; using to:".$target_arr[$waiting_count]["message_to"];
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
          if ($waiting_count < 0){
          
               // fwrite($fh,"\n in waiting_count ==0 condition ".$waiting_count);
               // flush();
                
                $target_arr =null; //reset
                 
                //$qs="SELECT q.*,b.alias_email FROM mail_queue q,linkbuilders b where sent_status=0";
                //$qs.=" AND q.linkbuilder_id=b.linkbuilder_id";


                $mail_set=mysql_query($main_qs);  //main_qs defined up top
      
          	
                
            
                while ($list_rec = mysql_fetch_array($mail_set, MYSQL_ASSOC) ){
                       $target_arr []= $list_rec;
                }


                //$waiting_count =count($target_arr)-1;
                  $waiting_count =count($target_arr); //count corrected by decrment operator below
                  
                  if ($waiting_count==0){
                     //perpetuate infinite loop
                       
                       sleep(270); // ~4-5 mins check again for live
                      // sleep(30); // 30 secs check again for debug
                  }
          } //end if last row waiting_count==0
  
               
           $waiting_count--;
           if ($waiting_count<0){
             $waiting_count= -1;
           }
    
} //end while $waiting_count > 0


fclose($fh);
 
//delete("gen/mail_leak.loc");
exec("rm -f /home3/inkpalne/public_html/dealtracker/gen/mail_leak.loc");
  
//echo "\nDone 9\n";
   
/*
//function send_message(){

    // mail(string to, string subject, string message)
}

*/


?>
