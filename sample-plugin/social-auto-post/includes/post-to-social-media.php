<?php
?>
<style>
.order-tbl-wrap table {background-color: #F9F9F9;border:1px solid #DFDFDF; width:98%; margin-bottom:25px;}
.order-tbl-wrap table td {font-size: 12px;  padding: 5px 7px 5px; vertical-align: top; color:#555555; border-bottom:1px solid #DFDFDF;}
.order-tbl-wrap table tr:last-child td { border-bottom:0px;}  
</style>
<h3>Post To Social Media</h3>
<div class="order-tbl-wrap">
<table>
<tr>
	<td><a href="<?php echo add_query_arg(array('postedtype'=>'twitter')); ?>">Twitter</a></td>
	<td><a href="<?php echo add_query_arg(array('postedtype'=>'facebook')); ?>">Twitter</a></td>
</tr>
</table>
</div>