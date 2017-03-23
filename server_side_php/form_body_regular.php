<style>

  .closing{
   background-color: rgb(255,208,128);
   
 }
 .opp{
   background-color: rgb(204,255,255);
 }
 .deal{
   background-color: rgb(204,255,153);
 }
</style>



  <form name="mainform" method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
<input type="hidden" name="mode">

<table id="outer"> <tr> <td>

		<table cellpadding=3>
		
		
		
		
	
		
		
		<tr class="opp"><td align="right"><strong>Domain:</strong></td><td> <input name="domain_name"  <? preLoad("domain_name"); ?>  readonly="readonly" value="<?php echo $domain;?>"></td></tr>
		
		
		<tr class="opp"><td align="right">Assigned to:</td><td> 
		
		<?// echo "<p>before draw loop:_SESSION[primary_key]=".$_SESSION[$primary_key]; ?>
		
		<select onchange="" name="linkbuilder_id" >
		<option value=0> unclaimed </option>
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
		
<tr class="opp"><td align="right">Status:</td><td>
      <select name="deal_status"> 
     <option value="<? preLoadOption("deal_status"); ?>" selected> <?preLoadOption("deal_status"); ?></option>
      
      <?php require 'deal_status_options.php'; ?>
   
  </select></td></tr>
             
		
    <tr class="opp"><td align="right">Approved:</td><td>
      <select name="approved"> 
      <?php if  ( strlen($_SESSION["form_rec"]["approved"]) > 0 ){ ?>
     <option value="<? preLoadOption("approved"); ?>" selected> <?preLoadOption("approved"); ?></option>
     <?php
         }else{
       ?>
        <option value="Queued" selected> Queued</option>
        <?php
        }   //end else
        ?>
      <option value="Queued" >Queued</option>
	 <option value="Yes" >Yes</option>
     <option value="No" >No</option>
   
  </select></td></tr>
    	
       <tr class="opp"><td align="right">Email Addresses:</td><td> <textarea name="special_codes" rows="1" cols="25"><?php preLoadTextArea("special_codes"); ?></textarea></td></tr>
		  
		  <tr class="opp"><td align="right">Actions:</td><td>
      <select name="actions_status"> 
      <?php if  ( strlen($_SESSION["form_rec"]["actions_status"]) > 0 ){ ?>
     <option value="<? preLoadOption("actions_status"); ?>" selected> <?preLoadOption("actions_status"); ?></option>
     <?php
         }else{
       ?>
        <option value="" selected> </option>
        <?php
        }   //end else
        
        // now render options
          require 'actions_status_options.php';
          
        ?>
     
   
  </select></td></tr>
		 	
		 	
       <tr  class="opp"><td align="right">Notes:</td><td> <textarea name="notes" rows="2" cols="25"><?php preLoadTextArea("notes"); ?></textarea></td></tr>
		
		
		
        	<tr class="closing"><td align="right">Closing Task:</td><td>
      <select name="closing_task"> 
     <option value="<? preLoadOption("closing_task"); ?>" selected> <?preLoadOption("closing_task"); ?></option>
      
      <?php require 'closing_task_options.php'; ?>
   
  </select></td></tr>
        
        
        
          <tr class="deal"><td align="right">Posting Date:</td><td>
         <script>DateInput('posting_date', false, 'YYYY-MM-DD'<?php 
          if (strlen( $_SESSION["form_rec"]["posting_date"] )  >  5 ){
                 echo ",'".$_SESSION["form_rec"]["posting_date"]."'";} ?>)</script>
                 
          </td></tr>
			 
			  <tr class="deal"><td align="right">Keywords:</td><td> <input name="keywords"  <? preLoad("keywords"); ?>  ></td></tr>
			  <tr class="deal"><td align="right">KW Targets:</td><td> <input name="keyword_targets"  <? preLoad("keyword_targets"); ?>  ></td></tr>
			  <tr  class="deal"><td align="right">Article Loc.:</td><td> <textarea name="article_location" rows="2" cols="25"><?php preLoadTextArea("article_location"); ?></textarea></td></tr>
		
		    <tr class="deal"><td align="right">Components:</td><td> <input name="components"  <? preLoad("components"); ?>  ></td></tr>
			  <tr class="deal"><td align="right">Duration (months):</td><td> <input name="duration"  <? preLoad("duration"); ?>  ></td></tr>
        <tr class="deal"><td align="right">Deal Value:</td><td> <input name="deal_value"  <? preLoad("deal_value"); ?>  ></td></tr> 
             
        <tr class="deal"><td align="right">Deal Cost:</td><td> <input name="deal_cost"  <? preLoad("deal_cost"); ?>  ></td></tr>
             
		    <tr class="deal"><td align="right">Paypal Email:</td><td> <input name="paypal_email"  <? preLoad("paypal_email"); ?>  ></td></tr>

		  
		  
		  
		   <tr class="deal"><td align="right">Deal Date:</td><td>
         <script>DateInput('deal_date', false, 'YYYY-MM-DD'<?php 
          if (strlen( $_SESSION["form_rec"]["deal_date"] )  >  5 ){
                 echo ",'".$_SESSION["form_rec"]["deal_date"]."'";} ?>)</script>
                 
          </td></tr>
		  
		 
		  
		  
		  
		   
		  
		   
		
		<!--		<tr><td align="right">Last Action:</td><td> <input name="last_action"  <? preLoad("last_action"); ?>  readonly="readonly" ></td></tr> -->
		
		</table>
		
</td><td>



</td></tr>	</table>

	
<p>
<!--<button onclick="doCreate()">Create New Field</button>-->
  Saved today at: <?php echo $_SESSION["savetime"]; ?>&nbsp;&nbsp;&nbsp;&nbsp; <button onclick="doSave()">&nbsp;&nbsp;Save&nbsp;&nbsp;</button>

<br>




