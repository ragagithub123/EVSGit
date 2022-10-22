<?php

ob_start();
session_start();
include('../includes/functions.php');
$getlocdeatils=$db->joinquery("SELECT location.locationid,location.unitnum,location.street,location.suburb,location.city,agent.`unitnum` As agentUnitnum,agent.`street` AS agentStreet,agent.`suburb` AS agentSubrub,agent.`city` AS agentCity,customer.* FROM location,agent,customer WHERE location.agentid=agent.agentid  AND customer.customerid=location.customerid AND location.locationSearch='".$_POST['locationtext']."' AND location.agentid ='".$_SESSION['agentid']."'");
$rowdet=mysqli_fetch_array($getlocdeatils);
$Locationid = $rowdet['locationid'];

$flag = 0;
$getlayerCount = $db->joinquery("SELECT * FROM window_layers WHERE locationid='" . $rowdet['locationid'] . "'");
if(mysqli_num_rows($getlayerCount) == 0)
$flag = 1;



$get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.windowid,window.`selected_product` FROM `room`,`window` WHERE window.roomid=room.roomid AND room.locationid=".$rowdet['locationid']." ORDER BY room_name ASC");
$i=64;

while($row_window=mysqli_fetch_array($get_window)){
	
  $i=$i+1;
  
  if($row_window['selected_product']!='HOLD'){
    
  $get_panel =$db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.`glasstypeid`,panel.`styleid`,panel.safetyid,paneloption_safety.name AS safety_name,paneloption_style.evsGlassX,paneloption_style.evsGlassY,paneloption_style.retroGlassX,paneloption_style.retroGlassY FROM panel,paneloption_style,paneloption_safety WHERE 
panel.styleid=paneloption_style.styleid  AND panel.safetyid=paneloption_safety.safetyid AND panel.windowid=".$row_window['windowid']."");

if(mysqli_num_rows($get_panel)>0){

$j=0;
while($row_panel=mysqli_fetch_array($get_panel)){

$j++;

  $row_window['panels'] =$row_panel;

$row_window['ID']=chr($i).$j;
  
  $postpanel[]=$row_window;

 
}
      
}

    
    
} // if

} // while window


foreach($postpanel as $row_panes)
{  
      if($row_panes['selected_product']=="EVSx3" || $row_panes['selected_product']=="EVSx2")
            {
                  
                  $glassX=$row_panes['panels']['evsGlassX'];
                  $glassY=$row_panes['panels']['evsGlassY'];
                
                
                  
              }
              else{
                
                  $glassX=$row_panes['panels']['retroGlassX'];
                  $glassY=$row_panes['panels']['retroGlassY'];
                
                
                  
                  }
                    if($glassX == NULL)$glassX=0;
                    if($glassY == NULL)$glassY=0;

                    $glassWidth = $row_panes['panels']['width'] + $glassX;
                    if(($row_panes['panels']['center']) > ($row_panes['panels']['height']))
                    $glassHeigt=($row_panes['panels']['center'])+$glassY;
                    else
                    $glassHeigt=($row_panes['panels']['height'])+$glassY;

                    $glassCenetr =0;

                    $row_panes['panels']['glassWidth'] = $glassWidth;
                    $row_panes['panels']['glassHeigt'] = $glassHeigt;
                    $row_panes['panels']['glassCenetr'] = $glassCenetr;
                    
                    
                      
                      if($flag == 1)
                   
                    $getlayer	= $db->joinquery("SELECT * FROM paneloption_layers WHERE glassType='".$row_panes['panels']['glasstypeid']."'");
                    
                    else
                    
                    $getlayer	= $db->joinquery("SELECT paneloption_layers.* FROM paneloption_layers,window_layers WHERE paneloption_layers.layersid=window_layers.layerid AND paneloption_layers.glassType=window_layers.glassid AND window_layers.glassid='" . $row_panes['panels']['glasstypeid'] . "' AND window_layers.locationid='" . $Locationid . "'");
              
                  $rowlayer =mysqli_fetch_array($getlayer);
                  
                  $row_panes['panels']['layer'] = $rowlayer['name'];

                 
                  $row_panes['panels']['InsideGlasscode'] = $rowlayer['InsideGlasscode'];
                  $row_panes['panels']['OutsideGlasscode'] = $rowlayer['OutsideGlasscode'];
                  $row_panes['panels']['compositeThickness']=$rowlayer['compositeThickness'];
                  $row_panes['panels']['sapcerWidth']=$rowlayer['sapcerWidth'];
                  
                  
                  
  
              $result_arr[]=$row_panes;
}


//print_r($result_arr);die();




$xmlString= '<?xml version="1.0" encoding="UTF-8"?>
    <Order>
	<fusioncustomername>'.$rowdet['firstname']." ".$rowdet['lastname'].'</fusioncustomername>
 <fusioncustomernumber>[DEFAULT]</fusioncustomernumber>
  <jobnumber>'.$rowdet['unitnum'].str_replace(' ', '',$rowdet['street']).'</jobnumber>
  <jobdescription>'.$rowdet['unitnum']." ".$rowdet['street'].'</jobdescription>
  <deliveryaddress>
  <street>'.$rowdet['agentStreet'].'</street>
  <suburb>'.$rowdet['agentSubrub'].'</suburb>
  <city>'.$rowdet['agentCity'].'</city>
  </deliveryaddress>';

$k=0;

foreach($result_arr as $row_panel)
{

  if($row_panel['selected_product']=="EVSx3" || $row_panel['selected_product']=="EVSx2")
	{

    $safetyarr = evsSafety($row_panel['panels']['safety_name']);
    $outersafety = $safetyarr[0];
    $innersafety = $safetyarr[1];

  }

  else{

    $safetyarr = sguSafety($row_panel['panels']['safety_name']);
    $outersafety = $safetyarr[0];
    $innersafety = $safetyarr[1];
  }


 if($innersafety == "Tuff")
 $innerglasscode = $row_panel['panels']['InsideGlasscode'].'T';
 else
 $innerglasscode = $row_panel['panels']['InsideGlasscode'];
 if($outersafety == "Tuff")
 $outerglasscode = $row_panel['panels']['OutsideGlasscode'].'T';
 else
 $outerglasscode = $row_panel['panels']['OutsideGlasscode'];
   $k++;

   $xmlString.='<orderline>
   <linenumber>'.$k.'</linenumber>
   <linetype>U</linetype>
   <quantity>1</quantity>
   <leftheight>'.$row_panel['panels']['glassHeigt'].'</leftheight>
   <centreheight>'.$row_panel['panels']['glassCenetr'].'</centreheight>
   <rightheight>'.$row_panel['panels']['glassHeigt'].'</rightheight>
   <base>'.$row_panel['panels']['glassWidth'].'</base>
   <innerglasscode>'.$innerglasscode.'</innerglasscode>
 <innerglassdescription> '.$row_panel['panels']['layer'].'</innerglassdescription>
 <outerglasscode>'.$outerglasscode.'</outerglasscode>
 
 <outerglassdescription> '.$row_panel['panels']['layer'].'</outerglassdescription>
 <spacer>'.trim($row_panel['panels']['sapcerWidth'],'mm').' None</spacer>
 
 <unitthickness>'.trim($row_panel['panels']['compositeThickness'],'mm').'</unitthickness>
 <stamp></stamp>
 
 <marks>'.$row_panel['room_name'].'</marks>
 <itemnumber>'.$k.'</itemnumber>
 <panenumber>'.$row_panel['ID'].'</panenumber>
 <bin>'.$row_panel['ID'].'</bin>
 <usage>S</usage>
   </orderline>';

}


 

$xmlString.= '</Order>';

$dom = new DOMDocument;
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($xmlString);
$journalName = $rowdet['unitnum'].str_replace(' ', '',$rowdet['street']);//str_replace(',', '_', $_POST['locationtext']);

//Save XML as a file
$dom->save($gXmlDir.$journalName.'.xml');
echo $dom->saveXML();


function evsSafety($Safety){
				
  if ($Safety == "Door"){
 $SafetyOutside = "Tuff";
 $SafetyInside  =   "No";
}else if($Safety == "Window"){
$SafetyOutside = "No";
$SafetyInside  = "No";
}else{
$SafetyOutside = "No";
$SafetyInside  =  "No";
}
  
  return array($SafetyOutside,$SafetyInside);
  
}
function sguSafety($Safety){
  if ($Safety == "Door"){
 $SafetyOutside = "Tuff";
 $SafetyInside  =   "Tuff";
}else if($Safety == "Window"){
$SafetyOutside = "No";
$SafetyInside  = "Tuff";
}else{
$SafetyOutside = "No";
$SafetyInside  =  "No";
}
  
    return array($SafetyOutside,$SafetyInside);
  
}

?>