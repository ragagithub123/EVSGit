<?php ob_start();

session_start();

include ('includes/functions.php');

if (!empty($_SESSION['agentid']))
{


	
	  $gethourrate = $db->joinquery("SELECT labourrate FROM agent WHERE agentid='".$_SESSION['agentid']."'");
			
			$rowhourrate = mysqli_fetch_array($gethourrate);
			
			$labourrate = $rowhourrate['labourrate'];

    $getstyles = $db->joinquery("SELECT styleid,name FROM paneloption_style ORDER BY name ASC");

    while ($row_style = mysqli_fetch_array($getstyles))
    {

        $post_style[] = $row_style;

    }

    $getwindowoption = $db->joinquery("SELECT * FROM products WHERE agentid='" . $_SESSION['agentid'] . "'");

    if (mysqli_num_rows($getwindowoption) == 0)
    {

        $getwindowoption = $db->joinquery("SELECT * FROM products WHERE agentid='1'");

    }

    while ($window_op = mysqli_fetch_array($getwindowoption))
    {

        if ($window_op['imageid'] == 0) $window_op['imageid'] = $window_op['productid'];

        $windowOption[] = $window_op;

    }

    $getprop = $db->joinquery("SELECT locationSearch FROM location WHERE agentid='" . $_SESSION['agentid'] . "'");

    if (mysqli_num_rows($getprop) > 0)
    {

        while ($row_prop = mysqli_fetch_array($getprop))
        {

            $postLocation[] = $row_prop['locationSearch'];

        }

    }

    if (isset($_REQUEST['id']))
    {

        $Locationid = base64_decode($_REQUEST['id']);
        $RoomList =array();

        $getRooms = $db->joinquery("SELECT roomid,name FROM room WHERE locationid='".$Locationid."'");
        while($moveRooms = mysqli_fetch_assoc($getRooms)){
            $RoomList[]=$moveRooms;
        }

    }
    

    if (!empty($Locationid))
    {

        $projects =array();

         $getSelectedPjts = $db->joinquery("SELECT * FROM location_projects WHERE locationid='".$Locationid."'");

         while($rowSelpjt = mysqli_fetch_assoc($getSelectedPjts)){
 
               $projects[] = $rowSelpjt;
         }

        $get_details = $db->joinquery("SELECT location.locationid,location.projectid,location.`unitnum`,location.`street`,location.`suburb`,location.`city`,`location`.locationstatusid,location.notes,location.photoid,location.`status1`,location.`status2`,location.status3,location.status4,location.status5,location.status6,location.status7,location.status8,location.status9,location.status10,location.status11,location.status12,location.status13,location.status14,location.status15,location.hs_overheadpower,location.hs_siteaccess_notes,location.hs_vegetation_notes,location.hs_heightaccess_notes,location.hs_heightaccess_photoid,location.hs_childrenanimals_notes,location.hs_traffic_notes,location.hs_weather_notes,location.hs_worksite_notes,location.distance,location.travel_rate,location.`quotesdg`,location.`quotemaxe`,location.`quotexcle`,location.`quoteevsx2`,location.`quoteevsx3`,customer.customerid,customer.firstname,customer.lastname,customer.email,customer.phone,agent.labourrate,photo.width,photo.height FROM location LEFT JOIN photo ON location.photoid=photo.photoid ,agent,customer WHERE location.agentid=agent.agentid AND location.customerid=customer.customerid AND location.`locationid`=" . $Locationid . "");

        $row_details = mysqli_fetch_array($get_details);

        if(!empty($row_details['projectid']) && ($row_details['projectid']!=NULL)){

            $getproject = $db->joinquery("SELECT project_name,projectid,project_date FROM location_projects WHERE projectid='".$row_details['projectid']."'");

            $rowPjt =  mysqli_fetch_array($getproject);

            $row_details['project_name'] = $rowPjt['project_name'];

            $row_details['projectid'] = $rowPjt['projectid'];

            $row_details['project_date'] = date('d-m-Y',strtotime($rowPjt['project_date']));

            $getallwindow = $db->joinquery("SELECT windowid FROM window,room,location WHERE room.locationid=location.locationid AND room.roomid=window.roomid AND location.locationid='".$Locationid."'");

           while($rowallwindow = mysqli_fetch_array($getallwindow)){

            $getPdt = $db->joinquery("SELECT product,projectid FROM projects_products WHERE locationid='".$Locationid."' AND projectid='".$rowPjt['projectid']."' AND windowid='".$rowallwindow['windowid']."'"); 

            if(mysqli_num_rows($getPdt)>0){

            $rowPdt = mysqli_fetch_array($getPdt);


            $db->joinquery("Update window SET projectid='".$rowPjt['projectid']."',selected_product='".$rowPdt['product']."' WHERE windowid='".$rowallwindow['windowid']."'");
            }
            else{

                $db->joinquery("Update window SET selected_product='HOLD',projectid='".$rowPjt['projectid']."' WHERE windowid='".$rowallwindow['windowid']."'");  
            }

           }

        

          $get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS total_panels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid='" . base64_decode($_REQUEST['id']) . "'  AND window.projectid='".$rowPjt['projectid']."'");

          $get_selected_cnt = $db->joinquery("SELECT sum(window_type.numpanels) AS pdt_count FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . base64_decode($_REQUEST['id']) . "' AND window.projectid='".$rowPjt['projectid']."'");

          $get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.quote_status,window.notes,window.extras,window.windowid,window.`costSGU`,window.`costIGUX2`,window.`costIGUX3`,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3,window.windowtypeid,window.status,window.`selected_product`,window.`selected_price`,window.`selected_hours`,window.`materialCategory`,window_type.name,window_type.numpanels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid=" . $Locationid . " AND window.projectid='".$rowPjt['projectid']."' GROUP BY window.windowid ORDER BY room_name ASC");


        }

        else{

            $get_totalpanel = $db->joinquery("SELECT sum(window_type.numpanels) AS total_panels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid='" . base64_decode($_REQUEST['id']) . "'");

            $get_selected_cnt = $db->joinquery("SELECT sum(window_type.numpanels) AS pdt_count FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND window.selected_product!='HOLD' AND room.locationid='" . base64_decode($_REQUEST['id']) . "'");

            $get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.quote_status,window.notes,window.extras,window.windowid,window.`costSGU`,window.`costIGUX2`,window.`costIGUX3`,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3,window.windowtypeid,window.status,window.`selected_product`,window.`selected_price`,window.`selected_hours`,window.`materialCategory`,window_type.name,window_type.numpanels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid AND room.locationid=".$Locationid ." GROUP BY window.windowid ORDER BY room_name ASC");
        }


        $row_quote = mysqli_fetch_array($get_totalpanel);

        $row_selected = mysqli_fetch_array($get_selected_cnt);

        $row_details['total_panels'] = $row_quote['total_panels'];

        $row_details['pdt_count'] = $row_selected['pdt_count'];

        

        $array_location = $row_details;

        $get_status = $db->joinquery("SELECT * FROM location_status");

        while ($row_status = mysqli_fetch_array($get_status))
        {

            $array_status[] = $row_status;

        }

        $jobstatus = $db->joinquery("SELECT * FROM jobstatus");

        while ($row_jobstatus = mysqli_fetch_array($jobstatus))
        {

            $array_jostatus[] = $row_jobstatus;

        }

        $get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.quote_status,window.notes,window.extras,window.windowid,window.`costSGU`,window.`costIGUX2`,window.`costIGUX3`,window.costsdg,window.costmaxe,window.costxcle,window.costevsx2,window.costevsx3,window.windowtypeid,window.status,window.`selected_product`,window.`selected_price`,window.`selected_hours`,window.`materialCategory`,window_type.name,window_type.numpanels FROM room,window,window_type WHERE window.roomid=room.roomid AND window.windowtypeid=window_type.windowtypeid  AND room.locationid=" . $Locationid . " GROUP BY window.windowid ORDER BY room_name ASC");

        while ($row_window = mysqli_fetch_array($get_window))
        {

            // 			$selectedpdts[] = $row_window['selected_product'];
            $getextra = $db->joinquery("SELECT sum(cost) AS extravalue FROM window_extras WHERE windowid=" . $row_window['windowid'] . "");

            if (mysqli_num_rows($getextra) == 0)
            {

                $extravalue = 0;

            }

            else
            {

                $row_extra = mysqli_fetch_array($getextra);

                $extravalue = $row_extra['extravalue'];

                if ($extravalue == NULL)
                {

                    $extravalue = 0;

                }

            }

            $row_window['extravalue'] = $extravalue;

            # labour cost
            $getlabour = $db->joinquery("SELECT panelid,SUM(dglabour) AS igulabour, SUM(evslabour) AS evslabour FROM panel WHERE windowid = " . $row_window['windowid'] . " ORDER BY panelnum ASC");

            $row_labour = mysqli_fetch_array($getlabour);

            $row_window['evslabour'] = $row_labour['evslabour'];

            $row_window['igulabour'] = $row_labour['igulabour'];

            $row_window['panelid'] = $row_labour['panelid'];

            $gettravel = $db->joinquery("SELECT travel_status,distance,number_staff FROM location WHERE locationid='$Locationid'");

            $rowtravel = mysqli_fetch_array($gettravel);

            if ($rowtravel['travel_status'] == 0)
            {

                $row_window['travelSGU'] = 0;

                $row_window['travelIGUX2'] = 0;

                $row_window['travelIGUX3'] = 0;

                $row_window['travelEVSX2'] = 0;

                $row_window['travelEVSX3'] = 0;

            }
            else
            {

                $queryMargins = $db->joinquery("SELECT labourrate,travelrate FROM location_margins WHERE locationid='$Locationid'");

                $margins = mysqli_fetch_array($queryMargins);

                if (mysqli_num_rows($queryMargins) == 0)
                {

                    $queryMargins = $db->joinquery("SELECT labourrate,evsmargin,igumargin,productmargin,agenttravelrate as travelrate FROM agent WHERE agentid='" . $_SESSION['agentid'] . "'");

                    $margins = mysqli_fetch_array($queryMargins);

                    $db->joinquery("INSERT INTO location_margins(`locationid`,`evsmargin`,`igumargin`,`productmargin`,`labourrate`,`travelrate`)VALUES('$Locationid','" . $margins['evsmargin'] . "','" . $margins['igumargin'] . "','" . $margins['productmargin'] . "','" . $margins['labourrate'] . "','" . $margins['travelrate'] . "')");

                }

                $travelDaysEVS = ($row_labour['evslabour'] / (7 * $rowtravel['number_staff']));

                $travelHoursEVS = ((($rowtravel['distance'] * 2) * $rowtravel['number_staff']) / 90) * $travelDaysEVS;

                $row_window['travelEVSX2'] = $row_window['travelEVSX3'] = round((((($rowtravel['distance'] * 2) * $travelDaysEVS) * $margins['travelrate']) + ($travelHoursEVS * $margins['labourrate'])) , 2);

                $travelDaysIGU = ($row_labour['igulabour'] / (5 * $rowtravel['number_staff']));

                $travelHoursIGU = ((($rowtravel['distance'] * 2) * $rowtravel['number_staff']) / 90) * $travelDaysIGU;

                $row_window['travelSGU'] = $row_window['travelIGUX2'] = $row_window['travelIGUX3'] = round((((($rowtravel['distance'] * 2) * $travelDaysIGU) * $margins['travelrate']) + ($travelHoursIGU * $margins['labourrate'])) , 2);

            }

            if ($row_window['selected_product'] == "SGU" || $row_window['selected_product'] == "IGUX2" || $row_window['selected_product'] == "IGUx3" || $row_window['selected_product'] == "SDG" || $row_window['selected_product'] == "MAXe" || $row_window['selected_product'] == "XCLe")
            {

                $total_travel_cost[] = round((((($rowtravel['distance'] * 2) * $travelDaysIGU) * $margins['travelrate']) + ($travelHoursIGU * $margins['labourrate'])) , 2);

            }

            else if ($row_window['selected_product'] == "EVSx2" || $row_window['selected_product'] == "EVSx3")
            {

                $total_travel_cost[] = round((((($rowtravel['distance'] * 2) * $travelDaysEVS) * $margins['travelrate']) + ($travelHoursEVS * $margins['labourrate'])) , 2);

            }

            if (!empty($total_travel_cost[0]))
            {

                $row_window['travel_amt'] = array_sum($total_travel_cost);

            }
            else
            {

                $row_window['travel_amt'] = 0;

            }

            $panels = array();

            if ($row_window['selected_product'] != 'HOLD')
            {
													
													 $get_panel = $db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`colourid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue,colours.colourname,colours.colorcode FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition,colours WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.colourid = colours.colourid AND panel.windowid=" . $row_window['windowid'] . " ORDER BY panelnum ASC");
												
                /*$get_panel = $db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`colourid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=" . $row_window['windowid'] . "");*/
      
                while ($row_panel = mysqli_fetch_array($get_panel))
                {

                    $panels[] = $row_panel;

                    $glasstypeids[] = $row_panel['glasstypeid'];
																				
																				$colourid[] = $row_panel['colourid'];

                }

            }

            $row_window['panelarray'] = $panels;

            $photos_window = array();

            $after_window = array();

            $extras = array();

            $before_photos = $db->joinquery("SELECT photoid FROM window_photo WHERE windowid='" . $row_window['windowid'] . "'");

            while ($row_phot = mysqli_fetch_array($before_photos))
            {

                $photos_window[] = $row_phot;

            }

            $row_window['window_photos'] = $photos_window;

            $after_photos = $db->joinquery("SELECT photoid FROM window_after_photo WHERE windowid='" . $row_window['windowid'] . "'");

            while ($row_phot_after = mysqli_fetch_array($after_photos))
            {

                $after_window[] = $row_phot_after;

            }

            $row_window['after_window_photos'] = $after_window;

            $get_extras = $db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid='" . $row_window['windowid'] . "' ORDER BY extraid DESC");

            while ($row_extras = mysqli_fetch_array($get_extras))
            {

                if ($row_extras['imageid'] == 0) $row_extras['imageid'] = $row_extras['productid'];

                $extras[] = $row_extras;

            }

            $row_window['extras'] = $extras;

            $array_windows[] = $row_window;

        }
        /* layer sections*/
								
								

        if (count($glasstypeids) > 0)
        {

            $resids = array_unique($glasstypeids);

            $glassidjoin = join(',', $resids);
												
												/* checking the layer was selected earlier */

            $getlayerCount = $db->joinquery("SELECT * FROM window_layers WHERE locationid='" . $Locationid . "'");

            if (mysqli_num_rows($getlayerCount) == 0)
            {

                $getglasstype = $db->joinquery("SELECT * FROM paneloption_glasstype WHERE glasstypeid IN($glassidjoin)");

                while ($rowglass = mysqli_fetch_assoc($getglasstype))
                {

                    $getlayers = $db->joinquery("SELECT `layersid`,`icon`,`name`,`glassType`,`outsideGlasstype`,`outsideThickness`,`spacerColor`,`sapcerWidth`,`insideGlasstype`,`insideThickness` FROM `paneloption_layers` WHERE glassType='" . $rowglass['glasstypeid'] . "' ORDER BY layersid ASC");

                    $layers = array();

                    $i = 0;

                    while ($row_layers = mysqli_fetch_assoc($getlayers))
                    {

                        $post['layer_id'] = $row_layers['layersid'];

                        $post['name'] = $row_layers['name'];

                        $layers[] = $post;

                        $glasstypeid[] = $row_layers['glassType'];

                        if ($i == 0)
                        {

                            $layerdetails['icon'] = $row_layers['icon']; 

                            $layerdetails['outsideThickness'] = $row_layers['outsideThickness'];

                            $layerdetails['insideThickness'] = $row_layers['insideThickness'];

                            $layerdetails['sapcerWidth'] = $row_layers['sapcerWidth'];

                            $getcolor = $db->joinquery("SELECT * FROM sapcercolor WHERE colourid ='" . $row_layers['spacerColor'] . "'");

                            $rowcolor = mysqli_fetch_array($getcolor);

                            $splitup = explode(' ', $rowcolor['colourname']);

                            $spcercolor = explode('(', $splitup[1]);

                            $spacecolor = explode(')', $spcercolor[1]);

                            $layerdetails['spacer'] = $spacecolor[0];

                            $layerdetails['short_spacer'] = $splitup[0];

                            $layerdetails['colorcode'] = $rowcolor['colorcode'];

                            $insideglass = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $row_layers['insideGlasstype'] . "'");

                            $rowglassinside = mysqli_fetch_array($insideglass);

                            $layerdetails['insideGlasstype'] = $rowglassinside['name'];

                            $outsideglass = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $row_layers['outsideGlasstype'] . "'");

                            $rowglassoutside = mysqli_fetch_array($outsideglass);

                            $layerdetails['outsideGlasstype'] = $rowglassoutside['name'];

                        }

                        $rowglass['layercomposite'] = $layers;

                        $rowglass['layerdetails'] = $layerdetails;

                        $i++;

                    }

                  
                    

                    $Glass[] = $rowglass;

                }

            }

            else
            {

                $getglasstype1 = $db->joinquery("SELECT * FROM paneloption_glasstype WHERE glasstypeid IN($glassidjoin)");

                while ($rowglass1 = mysqli_fetch_assoc($getglasstype1))
                {

                    $windowlayerdetails = $db->joinquery("SELECT paneloption_layers.* FROM paneloption_layers,window_layers WHERE paneloption_layers.layersid=window_layers.layerid AND paneloption_layers.glassType=window_layers.glassid AND window_layers.glassid='" . $rowglass1['glasstypeid'] . "' AND window_layers.locationid='" . $Locationid . "'");

                    $layers1 = array();

                    $post2 = array();
																				
																			 $post3  = array();
																				
																			
																				
																				if(mysqli_num_rows($windowlayerdetails)>0){

                    while ($row_layes = mysqli_fetch_assoc($windowlayerdetails))
                    {

                        $post1['layer_id'] = $row_layes['layersid'];

                        $post1['name'] = $row_layes['name'];

                        $getremaining_layers = $db->joinquery("SELECT `layersid` AS layer_id,`name` FROM `paneloption_layers` WHERE glassType='" . $rowglass1['glasstypeid'] . "' AND layersid!='" . $row_layes['layersid'] . "'");

                        while ($rowremiang = mysqli_fetch_assoc($getremaining_layers))
                        {

                            $post2[] = $rowremiang;

                        }

                        $layers1[] = $post1;

                        $glasstypeid[] = $row_layes['glassType'];
                        
                        $layerdetails1['icon'] = $row_layes['icon']; 

                        $layerdetails1['layersid'] = $row_layes['layersid'];

                        $layerdetails1['outsideThickness'] = $row_layes['outsideThickness'];

                        $layerdetails1['insideThickness'] = $row_layes['insideThickness'];

                        $layerdetails1['sapcerWidth'] = $row_layes['sapcerWidth'];

                        $getcolor1 = $db->joinquery("SELECT * FROM sapcercolor WHERE colourid ='" . $row_layes['spacerColor'] . "'");

                        $rowcolor1 = mysqli_fetch_array($getcolor1);

                        $splitup1 = explode(' ', $rowcolor1['colourname']);

                        $spcercolor1 = explode('(', $splitup1[1]);

                        $spacecolor1 = explode(')', $spcercolor1[1]);

                        $layerdetails1['spacer'] = $spacecolor1[0];

                        $layerdetails1['short_spacer'] = $splitup1[0];

                        $layerdetails1['colorcode'] = $rowcolor1['colorcode'];

                        $insideglass1 = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $row_layes['insideGlasstype'] . "'");

                        $rowglassinside1 = mysqli_fetch_array($insideglass1);

                        $layerdetails1['insideGlasstype'] = $rowglassinside1['name'];

                        $outsideglass1 = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $row_layes['outsideGlasstype'] . "'");

                        $rowglassoutside1 = mysqli_fetch_array($outsideglass1);

                        $layerdetails1['outsideGlasstype'] = $rowglassoutside1['name'];

                        $rowglass1['layercomposite'] = array_merge($layers1, $post2);

                        $rowglass1['layerdetails'] = $layerdetails1;

                    }

																}
																
																else{
																	
																	  /* selecting the layer which was not in window_layer but in glass type ..  may be  updating the panel glass type*/

                    $getlayers2 = $db->joinquery("SELECT `layersid`,`icon`,`name`,`glassType`,`outsideGlasstype`,`outsideThickness`,`spacerColor`,`sapcerWidth`,`insideGlasstype`,`insideThickness` FROM `paneloption_layers` WHERE glassType='" . $rowglass1['glasstypeid'] . "' ORDER BY layersid ASC");

                    $layers2 = array();

                    $k = 0;

                    while ($row_layers2 = mysqli_fetch_assoc($getlayers2))
                    {

                        $post3['layer_id'] = $row_layers2['layersid'];

                        $post3['name'] = $row_layers2['name'];

                        $layers2[] = $post3;

                        $glasstypeid2[] = $row_layers2['glassType'];

                        if ($k == 0)
                        {

                            $layerdetails2['icon'] = $row_layers2['icon']; 

                            $layerdetails2['outsideThickness'] = $row_layers2['outsideThickness'];

                            $layerdetails2['insideThickness'] = $row_layers2['insideThickness'];

                            $layerdetails2['sapcerWidth'] = $row_layers2['sapcerWidth'];

                            $getcolor2 = $db->joinquery("SELECT * FROM sapcercolor WHERE colourid ='" . $row_layers2['spacerColor'] . "'");

                            $rowcolor2 = mysqli_fetch_array($getcolor2);

                            $splitup2 = explode(' ', $rowcolor2['colourname']);

                            $spcercolor2 = explode('(', $splitup2[1]);

                            $spacecolor2 = explode(')', $spcercolor2[1]);

                            $layerdetails2['spacer'] = $spacecolor2[0];

                            $layerdetails2['short_spacer'] = $splitup2[0];

                            $layerdetails2['colorcode'] = $rowcolor2['colorcode'];

                            $insideglass2 = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $row_layers2['insideGlasstype'] . "'");

                            $rowglassinside2 = mysqli_fetch_array($insideglass2);

                            $layerdetails2['insideGlasstype'] = $rowglassinside2['name'];

                            $outsideglass2 = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $row_layers2['outsideGlasstype'] . "'");

                            $rowglassoutside2 = mysqli_fetch_array($outsideglass2);

                            $layerdetails2['outsideGlasstype'] = $rowglassoutside2['name'];

                        }

                        $rowglass1['layercomposite'] = $layers2;

                        $rowglass1['layerdetails'] = $layerdetails2;

                        $k++;

                    }

                  
                    

                   

                
																	
																	
																}

                    $Glass[] = $rowglass1;

                }

            }

        }

        else
        {

            $Glass = array();

        }

        /* end layer*/
								
						/* paint specifications*/
						
							$panelcount =array();
											
							$timecount =array();
							
							$costcount =array();
							
							$totalcostcount =array();
							
											
						
						if(count($colourid)>0){
							
							
							$replicated = array_count_values($colourid);
							
							$uniquecolor = array_unique($colourid);
							
							$joincolor = join(',',$uniquecolor);
							
							
							$getcolour = $db->joinquery("SELECT `colourid`,`colourname`,`colorcode` FROM colours WHERE colourid IN($joincolor)");
							
							if(mysqli_num_rows($getcolour)>0){
								
								 while($rowcolor = mysqli_fetch_assoc($getcolour)){
										
										$rowcolor['countpanels'] = $replicated[$rowcolor['colourid']];
										
										$getspeific = $db->joinquery("SELECT * FROM paint_specifications WHERE locationid='".$Locationid."' AND colourid='".$rowcolor['colourid']."'");

										
										if(mysqli_num_rows($getspeific)>0){
											
										
											 while($row_paint = mysqli_fetch_array($getspeific)){
													
													if($row_paint['selected_status'] == 1){
														
														$panelcount[] = $rowcolor['countpanels'];
														
														$timecount[] = $row_paint['times'];
														
														$costcount[] = $row_paint['cost'];
														
														$totalcostcount[] = $row_paint['totalcost'];
														
														
													}
													
														
														/*$rowcolor['times']= $rowcolor['countpanels']*$row_paint['hoursperpanel'];
										
													$rowcolor['hoursperpanel']=$row_paint['hoursperpanel'];
													
													$rowcolor['costperpanel']=$row_paint['costperpanel'];
														
													$rowcolor['cost']=$rowcolor['countpanels']*$row_paint['costperpanel'];
															
													$rowcolor['total']=($rowcolor['times']*$rowcolor['hoursperpanel'])+$rowcolor['cost'];
													
													$rowcolor['selected'] = $row_paint['selected_status'];*/
													
														$rowcolor['times']= $row_paint['times'];
										
													$rowcolor['hoursperpanel']=$row_paint['hoursperpanel'];
													
													$rowcolor['costperpanel']=$row_paint['costperpanel'];
														
													$rowcolor['cost']=$row_paint['cost'];
															
													$rowcolor['total']=$row_paint['totalcost'];
													
													$rowcolor['selected'] = $row_paint['selected_status'];
														
								
														
													}
										
											
											
										}
										
										else{
											
											 	$rowcolor['times']=0.0;
										
													$rowcolor['hoursperpanel']=0.0;
													
													$rowcolor['costperpanel']=0.0;
														
													$rowcolor['cost']=0.0;
															
													$rowcolor['total']=0.0;
														
													$rowcolor['selected'] = 0;
													
											
											
										}
										
										
										
										$paints[] = $rowcolor;
										
											if(count($panelcount)>0)
												
												$total_panelcount = array_sum($panelcount);
												
										else	
										
										$total_panelcount =0;	
										
										if(count($timecount)>0)
												
												$total_timecount = array_sum($timecount);
												
										else	
										
										$total_timecount =0;	
										
										if(count($costcount)>0)
												
												$total_costcount = array_sum($costcount);
												
										else	
										
										$total_costcount =0;	
										
										if(count($totalcostcount)>0)
												
												$total_totalcostcount = array_sum($totalcostcount);
												
										else	
										
										$total_totalcostcount =0;	
												
										
										
									}
								
								
							}
							
						}
					
						
						/* end paint specifications*/		

    }
				
$userid =$_SESSION['agentid'];

    include ('templates/header.php');

    include ('views/manage-portal.htm');

    include ('templates/footer.php');

}

else
{

    header('Location:index.php');

}

