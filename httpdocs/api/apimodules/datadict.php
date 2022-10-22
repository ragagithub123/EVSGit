<?php
	
$gDataDict = array(
	'location' => array(
		'locationid' => 'i',
		'address' => 's',
		'customerid' => 'i',
		'agentid' => 'i',
		'locationstatusid' => 'i',
		'status' => 's',
		'unitnum' => 's',
		'street' => 's',
		'suburb' => 's',
		'city' => 's',
		'postcode' => 's',
		'housetype' => 's',
		'notes' => 's',
		'photoid' => 'i',
	
		'quotesdg' => 'i',
		'quotemaxe' => 'i',
		'quotexcle' => 'i',
		'quoteevsx2' => 'i',
		'quoteevsx3' => 'i',

		'quotegreeting' => 's',		
		'quotedetails' => 's',
		'quotelocked' => 'i',
	
		'hs_overheadpower' => 'i',
		'hs_overheadpower_notes' => 's',
		'hs_overheadpower_photoid' => 'i',
	
		'hs_steepdriveway' => 'i',
		'hs_stairs' => 'i',
		'hs_slipperyground' => 'i',
		'hs_siteaccess_notes' => 's',
		'hs_siteaccess_photoid' => 'i',
	
		'hs_rosebushes' => 'i',
		'hs_bushes' => 'i',
		'hs_gardens' => 'i',
		'hs_vegetation_notes' => 's',
		'hs_vegetation_photoid' => 'i',
	
		'hs_stepladder' => 'i',
		'hs_mobilescaffolding' => 'i',
		'hs_towerscaffolding' => 'i',
		'hs_windowharness' => 'i',
		'hs_heightaccess_notes' => 's',
		'hs_heightaccess_photoid' => 'i',

		'hs_smallchildren' => 'i',
		'hs_dogs' => 'i',
		'hs_insects' => 'i',
		'hs_childrenanimals_notes' => 's',
		'hs_childrenanimals_photoid' => 'i',

		'hs_footpath' => 'i',
		'hs_driveway' => 'i',
		'hs_roadway' => 'i',
		'hs_traffic_notes' => 's',
		'hs_traffic_photoid' => 'i',
	
		'hs_brokenglass' => 'i',
		'hs_chemicals' => 'i',
		'hs_mouldrot' => 'i',
		'hs_asbestos' => 'i',
		'hs_hazmat_notes' => 's',
		'hs_hazmat_photoid' => 'i',
	
		'hs_wind' => 'i',
		'hs_cold' => 'i',
		'hs_frost' => 'i',
		'hs_sun' => 'i',
		'hs_weather_notes' => 's',
		'hs_weather_photoid' => 'i',
	
		'hs_siteinduction' => 'i',
		'hs_hazards' => 'i',
		'hs_machinery' => 'i',
		'hs_worksite_notes' => 's',
		'hs_worksite_photoid' => 'i',
		'locationSearch'=>'s'
	),
	
	'customer' => array(
		'customerid' => 'i',
		'created' => 'i',
		'agentid' => 'i',
		'firstname' => 's',
		'lastname' => 's',
		'email' => 's',
		'phone' => 's',
	),
	
	'panel' => array(
		'panelid' => 'i',
		'windowid' => 'i',
		'panelnum' => 'i',
		'width' => 'i',
		'height' => 'i',
		'center' => 'i',
		'measurement' => 's',
		'safetyid' => 'i',
		'glasstypeid' => 'i',
		'styleid' => 'i',
		'conditionid' => 'i',
		'astragalsid' => 'i',
		'costsdg' => 'f',
		'costmaxe' => 'f',
		'costxcle' => 'f',
		'costevsx2' => 'f',
		'costevsx3' => 'f',
		'dglabour' => 'f',
		'evslabour' => 'f',
	),
	
	'room' => array(
		'roomid' => 'i',
		'locationid' => 'i',
		'name' => 's',
		'hazards' => 's',
		'notes' => 's',
	),
	
	'window' => array(
		'windowid' => 'i',
		'roomid' => 'i',
		'windowtypeid' => 'i',
		'name' => 's',
		'notes' => 's',
		'costsdg' => 'f',
		'costmaxe' => 'f',
		'costxcle' => 'f',
		'costevsx2' => 'f',
		'costevsx3' => 'f',
	),
	
	'windowtype' => array(
		'windowtypeid' => 'i',
		'name' => 's',
		'numpanels' => 'i',
	),
	
	'window_option' => array(
		'windowoptionid' => 'i',
		'name' => 's',
		'value' => 'f'
	),
	
	'paneloption_astragal' => array(
		'astragalsid' => 'i',
		'name' => 's',
		'astragalvalue' => 'f',
	),

	'paneloption_condition' => array(
		'conditionid' => 'i',
		'name' => 's',
		'conditionvalue' => 'f',
	),

	'paneloption_glasstype' => array(
		'glasstypeid' => 'i',
		'name' => 's',
		'typevalue' => 'f',
	),

	'paneloption_safety' => array(
		'safetyid' => 'i',
		'name' => 's',
		'safetyvalue' => 'f',
	),
	
	'paneloption_style' => array(
		'styleid' => 'i',
		'name' => 's',
		'styledgvalue' => 'f',
		'styleevsvalue' => 'f',
	
	),
	
	'location_status' => array(
		'locationstatusid' => 'i',
		'status' => 's',
	),

	'params' => array(
		'paramid' => 'i',
		'sdgglassrate' => 'f',
		'maxeglassrate' => 'f',
		'xcleglassrate' => 'f',
		'evsx2glassrate' => 'f',
		'evsx3glassrate' => 'f',
		'agentmargin' => 'f',
		'labourrate' => 'f',
		'travelrate' => 'f',
		'dgmaterials' => 'f',
		'evsmaterials' => 'f',
		'quotegreeting' => 's',
		'quotedetails' => 's',
	),
	
);


function CastMysqlTable($mysqlRow, $table) {
	global $gDataDict;
	
	$return = array();
	foreach($mysqlRow as $field => $value) {
		if(isset($gDataDict[$table][$field])) {
			switch($gDataDict[$table][$field]) {
				case 'i':
					$return[$field] = (int)$value;
					break;
					
				case 'f':
					$return[$field] = (float)$value;
					break;
				
				case 's':
					$return[$field] = $value;
					break;
			}
		}
	}
	
	return $return;
}


