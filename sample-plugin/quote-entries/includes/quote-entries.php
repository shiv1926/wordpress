<?php
global $wpdb;
$refrencenumber = $_REQUEST['refrencenumber'];
$datefrom = $_REQUEST['datefrom'];
$dateto = $_REQUEST['dateto'];
if(isset($_REQUEST['quotetype']) && !empty($_REQUEST['quotetype'])) {
	$quotetypearray = $_REQUEST['quotetype'];
	$quotetype = implode(",",$_REQUEST['quotetype']);
} else {
	$quotetypearray = array();
}

$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = 10;
$offset = ( $pagenum - 1 ) * $limit;

$searchsql='';
$searchsql.="SELECT * FROM quotes where 1=1 ";
if($refrencenumber!='') {
	$searchsql.=" and Number = '".$refrencenumber."'	";
}
if($quotetype!='') {
	$searchsql.=" and TypeID in (".$quotetype.") ";
}
if($datefrom!='') {
	$searchsql.=" and QuoteDate >='".$datefrom."'";
}
if($dateto!='') {
	$searchsql.=" and QuoteDate <='".$dateto."'";
}

$searchsql.=" order by Number desc LIMIT $offset, $limit";
//echo $searchsql;
$entries = $wpdb->get_results($searchsql);
echo '<div class="wrap">';
?>
<form method="get" action="">
<input type="hidden" name="page" value="quotes">
<div class="tablenav top">
<div class="alignleft actions">
Show Quote Types:
<input type="checkbox" name="quotetype[]" value="1" <?php if(in_array(1,$quotetypearray)) { echo 'checked="checked"'; } ?>>Truckload&nbsp;
<input type="checkbox" name="quotetype[]" value="2" <?php if(in_array(2,$quotetypearray)) { echo 'checked="checked"'; } ?>>LTL&nbsp;
<input type="checkbox" name="quotetype[]" value="3" <?php if(in_array(3,$quotetypearray)) { echo 'checked="checked"'; } ?>>Outbound&nbsp;
Ref#: <input type="search" value="<?php echo $refrencenumber; ?>" name="refrencenumber" id="post-search-input">
Date from: <input type="text" value="<?php echo $datefrom; ?>" name="datefrom" id="iddatefrom">
to: <input type="text" value="<?php echo $dateto; ?>" name="dateto" id="iddateto">
<input type="submit" value="Search" class="button" id="post-query-submit" name="filter_action">		
</div>
</div>
</form>

<table class="widefat">
	<thead>
		<tr>
			<th scope="col" class="manage-column column-name" style="">#</th>
			<th scope="col" class="manage-column column-name" style="">Type</th>
			<th scope="col" class="manage-column column-name" style="">Company</th>
			<th scope="col" class="manage-column column-name" style="">Person</th>
			<th scope="col" class="manage-column column-name" style="">Quote Date</th>
			<th scope="col" class="manage-column column-name" style="">Origin</th>
			<th scope="col" class="manage-column column-name" style="">Destination</th>
			<!-- <th scope="col" class="manage-column column-name" style="">Email</th> -->
			<!-- <th scope="col" class="manage-column column-name" style="">Ready Date</th> -->
			<!-- <th scope="col" class="manage-column column-name" style="">Detail</th> -->
		</tr>
	</thead>
	<tbody>
		<?php if( $entries ) { ?>

			<?php
			$count = 1;
			$class = '';
			foreach( $entries as $entry ) 
			{
				$class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
				$exp=explode(" ",$entry->QuoteDate);
				?>
				<tr<?php echo $class; ?>>
				<td>
					<a href="<?php echo add_query_arg('refrence',$entry->Number); ?>"><?php echo $entry->Number; ?></a>
				</td>
				<td><?php echo GetQuoteType($entry->TypeID); ?></td>
				<td><?php echo $entry->Company; ?></td>
				<td><?php echo $entry->FirstName." ".$entry->LastName; ?></td>
				<td><?php echo ShowDate($exp[0]); ?></td>
				<td>
					<?php 
					echo $entry->OrigZip;
					if($entry->OrigCity!='') {
						echo ", ".$entry->OrigCity;
					}
					if($entry->OrigState!='') {
						echo ", ".$entry->OrigState; 
					}
					?>
				</td>
				<td>
					<?php 
					echo $entry->DestZip;
					if($entry->DestCity!='') {
						echo ", ".$entry->DestCity;
					}
					if($entry->DestState!='') {
						echo ", ".$entry->DestState; 
					}
					?>
				</td>
				<!-- <td><?php echo $entry->Email; ?></td>
				<td><?php echo $entry->ReadyDate; ?></td>
				<td><a href="<?php echo add_query_arg('refrence',$entry->Number); ?>">Details</a></td> -->
				</tr>
				<?php
				$count++;
			}
			?>

		<?php } else { ?>
		<tr>
			<td colspan="2">No record found.</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php
$countsql="";

$countsql.="SELECT COUNT('Number') FROM quotes where 1=1 ";
if($refrencenumber!='') {
	$countsql.=" and Number = '".$refrencenumber."' ";
}
if($quotetype!='') {
	$countsql.=" and TypeID in (".$quotetype.") ";
}
if($datefrom!='') {
	$countsql.=" and QuoteDate >='".$datefrom."'";
}
if($dateto!='') {
	$countsql.=" and QuoteDate <='".$dateto."'";
}
$total = $wpdb->get_var($countsql);
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'prev_text' => __( '&laquo;', 'aag' ),
	'next_text' => __( '&raquo;', 'aag' ),
	'total' => $num_of_pages,
	'current' => $pagenum
) );

if ( $page_links ) {
	echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}

echo '</div>';