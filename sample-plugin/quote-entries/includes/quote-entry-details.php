<?php
global $wpdb;
$entries = $wpdb->get_results("SELECT * FROM quotes where Number = '".$_GET['refrence']."'");
$entry = $entries[0];
?>
<div class="wrap">
<table class="widefat">
	<tr><th>Type</th><td><?php echo GetQuoteType($entry->TypeID); ?></td></tr>
	<tr><th>Ready Date</th><td><?php echo $entry->ReadyDate; ?></td></tr>
	<?php if($entry->TypeID==1) { ?>
	<tr><th>Trailor Type</th><td><?php echo gettrailortype($entry->TrailerTypeID); ?></td></tr>
	<tr><th>Trailor Part Type</th><td><?php echo gettrailorparttype($entry->TrailerPartType); ?></td></tr>
	<tr><th>Feet Occupied</th><td><?php echo $entry->trailerFeet; ?></td></tr>
	<?php } ?>
	<tr><th>Origin Zip</th><td><?php echo $entry->OrigZip; ?></td></tr>
	<tr><th>Origin City</th><td><?php echo $entry->OrigCity; ?></td></tr>
	<tr><th>Origin State</th><td><?php echo $entry->OrigState; ?></td></tr>
	<tr><th>Liftgate required?</th><td><?php echo GetBool($entry->OrigLiftGate); ?></td></tr>
	<tr><th>Inside pickup required?</th><td><?php echo GetBool($entry->OrigInside); ?></td></tr>

	<?php if($entry->TypeID==3) { ?>
	<tr><th>Destination Country</th><td><?php echo $entry->DestCountryID; ?></td></tr>
	<tr><th>Destination City</th><td><?php echo $entry->DestCityName; ?></td></tr>
	<?php } ?>

	<tr><th>Destination Zip</th><td><?php echo $entry->DestZip; ?></td></tr>
	<?php if($entry->DestCity!='') { ?> 
	<tr><th>Destination City</th><td><?php echo $entry->DestCity; ?></td></tr>
	<?php } ?>
	<tr><th>Destination State</th><td><?php echo $entry->DestState; ?></td></tr>
	<tr><th>Liftgate required?</th><td><?php echo GetBool($entry->DestLiftGate); ?></td></tr>
	<tr><th>Inside pickup required?</th><td><?php echo GetBool($entry->DestInside); ?></td></tr>

	<tr><th>Mileage</th><td><?php echo $entry->Mileage; ?></td></tr>
	<tr><th>Price</th><td><?php echo $entry->Price; ?></td></tr>
	<tr><th>Days</th><td><?php echo $entry->Days; ?></td></tr>

	<tr><th>First Name</th><td><?php echo $entry->FirstName; ?></td></tr>
	<tr><th>Last Name</th><td><?php echo $entry->TypeID; ?></td></tr>
	<tr><th>Email</th><td><?php echo $entry->Email; ?></td></tr>
	<tr><th>Company</th><td><?php echo $entry->Company; ?></td></tr>
	<tr><th>Phone</th><td><?php echo $entry->Phone; ?></td></tr>
	<tr><th>Fax</th><td><?php echo $entry->Fax; ?></td></tr>

	<tr>
		<th>Item</th>
		<td>
			<?php 
			$jstring = json_decode($entry->Data); 
			$string = $jstring[0];
			//print_r($string);
			if($string->length!=''){
			echo "length: ".$string->length."<br>";
			}
			if($string->width!=''){
			echo "width: ".$string->width."<br>";
			}
			if($string->weight!=''){
			echo "weight: ".$string->weight."<br>";
			}
			if($string->height!=''){
			echo "height: ".$string->height."<br>";
			}
			if($string->description!=''){
			echo "description: ".$string->description."<br>";
			}
			if($string->packageType!=''){
			echo "package Type: ".$string->packageType."<br>";
			}
			if($string->commodityID!=''){
			echo "commodity: ".$string->commodityID."<br>";
			}
			if($string->count!=''){
			echo "Count: ".$string->count."<br>";
			}
			?>
		</td>
	</tr>
		<!-- <tr><th>Quote Data</th><td><?php echo GetQuoteType($entry->QuoteDate); ?></td></tr> -->
</table>
</div>