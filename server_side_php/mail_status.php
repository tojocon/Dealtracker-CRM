  <?php
/*                                                                             
 ****************************************************************************                                                                                
 * Filename: mail_status.php
 * 
 * Purpose:  display details of individual messages in queue
                                                                   
 *                                                             
 * Created:  8/15/12    
 *  
 * Author:   Toby Jones    
 *    
 * Notes:   
************************************************************************/  

 


 //make db connection
 // if (! $db_object){
 // $db_object= mysql_connect('localhost','toby', '11orion99');
 // mysql_select_db('stage2_inkpal_upgrade');
  
//  $db_object= mysql_connect('localhost','toby', '11orion99');
 // mysql_select_db('dealtracker');
 // }

 require 'db_connect.php';
 /* //clean data
  $ip=mysql_real_escape_string( $_GET["ip"] );

  if (strlen($ip) < 1 ){
  
    echo " No Ip address given";
  }
*/


$status_text['0']='waiting';
$status_text['1']='sent';
$status_text['2']='error';



     echo "<center><h3> Mail Status </h3>"; 
  ?>
 <br>

 <a href="javascript:self.close()">Close</a>
  <br>
  <?php 
     
  echo '<table border="0" id="container" width="100%"><tr><td style="width:90%;vertical-align: top;text-align:center;">';



//ini_set("display_errors","On");
//ini_set("", E_ALL)


if (!$db_object) {
    die('Could not connect: ' . mysql_error());
}
//echo 'Connected successfully';

 $qs = "SELECT q.*,b.alias_email from mail_queue q, linkbuilders b where q.linkbuilder_id=b.linkbuilder_id order by message_id desc limit 300";
 
 
// echo "<br>qs= $qs <br>";
 
  $set=mysql_query($qs);

  echo "<p>Newest 300 messages in queue.</p>";
  
  echo '<center><table border=1 cellpadding=5 cellspacing=0 width="90%" style="width:90%;"><tr>';
  //echo '<th>visited</th>';
  echo '<th width="30" style="width:30px;">Message id</th><th>Opp id</th><th>Subject</th>';
  echo '<th>To:</th><th>From:</th><th>status</th><th>modified</th></tr>';
  
  while($rec = mysql_fetch_array($set, MYSQL_ASSOC) ){
      
       // $ip=str_ireplace('12.202.40.236','Inkpal office IP', $rec["ip"]) ;
        
         
        echo "<tr>";
		//echo "<td>".$rec["visited_page"].'</td>';
		echo '<td width="30" style="width:30px;">'.$rec["message_id"]."</td><td>";
       echo $rec["opp_id"]."</td><td>";
    echo $rec["message_subject"]."</td><td>".$rec["message_to"]."</td><td>";
    echo $rec["alias_email"]."</td><td>".$status_text[ $rec["sent_status"] ];
     echo "</td><td>".$rec["date_queued"];
    echo "</td></tr>";
          
  } // end while
  
   echo "</table></center>";
 

$set=null;


 echo '</td>';
// echo '<td style="width:30%;vertical-align: top;">';

// echo '</td>';
 
 echo '</tr></table></center>';

mysql_close($db_object);
 

  ?>
  