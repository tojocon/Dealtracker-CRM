<?php
/*                                                                             
 ****************************************************************************                                                                                
 * Filename: singleton_controller.php
 *      
 * Purpose:  checks if a script is already running before starting it
 *            
 *                                                                                                                           
 * Created:  6/13/12   
 *  
 * Author:   Toby Jones    
 *  
 * updated:  
 *          
 *    
 * Notes:   
 *                                                                                    
************************************************************************/ 

$single_process_label= 'mail_leeker.php';

$cmd=" ps -ef | grep  $single_process_label";

$return = system($cmd);

echo "\n cmd returnd: $return"; 

echo "\n DONE"; 
