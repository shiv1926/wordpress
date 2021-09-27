<?php
function GetQuoteType($type)
{
	if($type==1) {
		$type='Truckload';
	} elseif($type==2) {
		$type='Less Than Truckload';
	} else {
		$type='International';
	}
	return $type;
} 

function gettrailortype($TrailerTypeID)
{
	if($TrailerTypeID==1) {
		$TrailerType = 'Dry Van';
	} elseif($TrailerTypeID==2) {
		$TrailerType = 'Refrigerated';
	} elseif($TrailerTypeID==3) {
		$TrailerType = 'Flatbed';
	} elseif($TrailerTypeID==4) {
		$TrailerType = 'Specialized';
	} else {
		$TrailerType = '';
	}
	return $TrailerType;
}

function gettrailorparttype($TrailerPartType)
{
	if($TrailerPartType==1) {
		$TrailerTypeName = 'Full Trailer';
	} elseif($TrailerPartType==2) {
		$TrailerTypeName = 'Partial Trailer';
	} else {
		$TrailerTypeName = '';
	}
	return $TrailerTypeName;
}

function GetBool($value)
{
	if($value>0) {
		$retrun="TRUE";
	} else {
		$retrun="FALSE";
	}
	return $retrun;
}

function ShowDate($date)
{
	$ndate = explode("-",$date);
	return $ndate[1]."/".$ndate[2]."/".$ndate[0];
}
?>