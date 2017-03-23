<?php
?>
<table cellpadding=2>
		
		    <tr><td colspan="2"><h4>1) Search Filters:</h4></td></tr>
			
			<tr><td align="right">Domain:</td><td> <input name="domain_name"  <? preLoad("domain_name"); ?> ></td></tr>
		
		
		<tr><td align="right">Assigned to:</td><td> 
		
		<?// echo "<p>before draw loop:_SESSION[primary_key]=".$_SESSION[$primary_key]; ?>
		
		<select onchange="" name="linkbuilder_id" >
		<option value="-1"> All </option>
		<option value="0"> unclaimed </option>
		<!--	<option value=<?php preLoadOption("linkbuilder_id")?> > <?php preLoadOption("name_first")?> <?php preLoadOption("name_last")?></option> -->
			
			
			
		
		<?
		////////////////////////////
		//
		//   TODO:  customize query
		//
		//////////////////////////
		
		$qs="select linkbuilder_id, name_first, name_last from linkbuilders";
		
		
		
		$f_set=mysql_query($qs);
		
		if(mysql_error()) {
		
			echo("error in select fill: ".mysql_error());
			//die($f_set->getMessage() );
		}
		
		  
		while($f_rec= mysql_fetch_array($f_set, MYSQL_ASSOC)  ){
		    
		    if ($_SESSION["form_rec"]["linkbuilder_id"]==$f_rec["linkbuilder_id"]){
		        echo "<option value=".$f_rec["linkbuilder_id"]." selected>".$f_rec["name_first"]." ".$f_rec["name_last"]."\n";
		    }else{
		        echo "<option value=".$f_rec["linkbuilder_id"].">".$f_rec["name_first"]." ".$f_rec["name_last"]."\n";
		    }
		    
		}//end while
		
		
		?>
			
			
			
			
		<?
		////////////////////////////
		//
		//   TODO:  customize query
		//
		//////////////////////////
/*		$qs="select $primary_key,$shortname from " .$maintable;
		
		
		
		$f_set=$db_object->query($qs);
		
		if(DB::isError($f_set)) {
		
			echo("error ".$f_set->getMessage() );
			//die($f_set->getMessage() );
		}
		
		  
		while($f_rec=$f_set->fetchrow()){
		    
		    if ($_SESSION[$primary_key]==$f_rec[$primary_key]){
		        echo "<option value=".$f_rec[$primary_key]." selected>".$f_rec[$shortname]."\n";
		    }else{
		        echo "<option value=".$f_rec[$primary_key].">".$f_rec[$shortname]."\n";
		    }
		    
		    
		}//end while
		
		    */
		?>
		</td></tr>
		
		
	
		
		<!--<tr><td colspan=2 align="center">-- optional --</td></tr>-->
		
		
		<!--<tr><td align="right">Status:</td><td> <input name="deal_status"  <? preLoad("deal_status"); ?> ><font color="red">*</font></td></tr>-->
		
<tr><td align="right">Status:</td><td>
      <select name="deal_status"> 
     <option value="<? preLoadOption("deal_status"); ?>" selected> <?preLoadOption("deal_status"); ?></option>
     <option value=" " >Any Status</option>
     
	<?php
	   require 'deal_status_options.php';
	?>
     
  </select></td></tr>


  <tr><td align="right">Actions:</td><td>
      <select name="actions_status"> 
     <option value="<? preLoadOption("actions_status"); ?>" selected> <?preLoadOption("actions_status"); ?></option>
     <option value=" " >Any Actions</option>
     
	<?php
	   require 'actions_status_options.php';
	?>
     
  </select></td></tr>


  
  <tr><td align="right">Closing Task:</td><td>
      <select name="closing_task"> 
     <option value="<? preLoadOption("closing_task"); ?>" selected> <?preLoadOption("closing_task"); ?></option>
     <option value=" " >Any</option>
     
	<?php
	   require 'closing_task_options.php';
	?>
     
  </select></td></tr>



		 <tr><td align="right">Notes:</td><td> <input name="notes" <?php preLoad("notes"); ?>></td></tr>
		
		
		 <tr><td align="right">Last Action Start:</td><td>
         <script>DateInput('last_action_start', false, 'YYYY-MM-DD'<?php 
          if (strlen( $_SESSION["form_rec"]["last_action_start"] )  >  5 ){
                 echo ",'".$_SESSION["form_rec"]["last_action_start"]."'";}
				 //else{ echo ",'00-00-0000'";} //end phpelse
    				 ?>)</script>
                 
          </td></tr>
		   
		  
		   <tr><td align="right">Last Action End:</td><td>
         <script>DateInput('last_action_end', false, 'YYYY-MM-DD'<?php 
          if (strlen( $_SESSION["form_rec"]["last_action_end"] )  >  5 ){
                 echo ",'".$_SESSION["form_rec"]["last_action_end"]."'";}
                 //else{ echo ",'00-00-0000'";} //end phpelse
				 ?>)</script>
                 
          </td></tr>
		   
		   
		<tr><td align="right"><button onclick="doRecent()">Reset</button></td><td><button onclick="doFilter()">Search</button></td></tr>
				
		</table>
