<?php die ( "WTF die to verfiy call"); ?>

<form name="mainform" method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
<input type="hidden" name="mode">
  <input type="hidden" name="linkbuilder_id" value="3" />
<table id="outer"> <tr> <td>

		<table cellpadding=3>
		
			<tr><td align="right">Domain:</td><td> <input name="domain_name"  <? preLoad("domain_name"); ?>  readonly="readonly" value="<?php echo $domain;?>"></td></tr>
		
		
	<!--	<tr><td align="right"><font color="green">Submitted by</font>:</td><td> 
		
		<?// echo "<p>before draw loop:_SESSION[primary_key]=".$_SESSION[$primary_key]; ?>
		
		<select onchange="" name="linkbuilder_id" >
		<option value=3> Monique </option>
		</select>
		</td></tr>   -->
		
		
	
		
		<!--<tr><td colspan=2 align="center">-- optional --</td></tr>-->
		
		
		<!--<tr><td align="right">Status:</td><td> <input name="deal_status"  <? preLoad("deal_status"); ?> ><font color="red">*</font></td></tr>-->
		
<tr><td align="right">&nbsp;</td><td>
        <input type="hidden" name="deal_status" value="New Opportunity"/>
      <!--<select name="deal_status"> 
     <option value="<? preLoadOption("deal_status"); ?>" selected> <?preLoadOption("deal_status"); ?></option>
      <option value="New Opportunity" >New Opportunity</option>
	 <option value="Deal" >Deal</option>
     <option value="Positive" >Positive</option>
     <option value="Pending" >Pending</option>
     <option value="No Reply" >No Reply</option>
     <option value="No Deal" >No Deal</option>
   
  </select> -->
     </td></tr>
             
			  
			 
            <tr><td align="right"><b>Posting Fee:</b></td><td> <input name="posting_fee"  <? preLoad("posting_fee"); ?>  ></td></tr>
		   
		   <tr><td align="right"><b>Special Codes:</b></td><td> <input name="special_codes"  <? preLoad("special_codes"); ?>  ></td></tr>
		  
		 	<tr><td align="right">Notes [optional]:</td><td> <textarea name="notes" rows="4" cols="25">  <?php preLoadTextArea("notes"); ?> </textarea><font color="red">*</font></td></tr>
		
		   
			
		</table>
		
</td><td>



</td></tr>	</table>

	
<p>
<!--<button onclick="doCreate()">Create New Field</button>-->
<?PHP




echo "<p>sess_dealstatus=".$_SESSION["form_rec"]["deal_status"];

 if ( strlen($_SESSION["form_rec"]["deal_status"]) > 1 ) { ?>
<button onclick="doSave()">Submit</button>

<?PHP  } else { ?>

<div style="background-color:red;width:250px;height:400px;">
  <h1> Already Submitted </h1>
</div>

<?PHP  } ?>
&nbsp;&nbsp;&nbsp;&nbsp;

<p>
&nbsp;
<p>
 <br>
