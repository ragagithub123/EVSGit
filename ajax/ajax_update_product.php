<?php ob_start();
session_start();
include('../includes/functions.php');
$gWebsite = "http://evsapp.nz";
$gWebsiteDir = "/var/www/vhosts/evsapp.nz/httpdocs/";
$gPanelOptionsPhotoURL = $gWebsite . "/assets/app/paneloptions/";
$gPanelOptionsPhotoDir = $gWebsiteDir . "/assets/app/paneloptions/";
$gProfilePhotoURL = $gWebsite . "/assets/app/profiles/";
$gProfilePhotoDir = $gWebsiteDir . "/assets/app/profiles/";
if ($_POST['pagestatus'] == 0) $popupid = "myModalExtraView";
else $popupid = "#myModalExtra";
if ($_POST['projectid'] == 0) {
    $ins_data = array(
        'locationid' => $_POST['locationid'],
        'project_name'     => $_POST['pjtname'],
        'project_date' => $_POST['pjtdate']
    );
    $pjtid = $db->ins_rec("location_projects", $ins_data);
    $inspjt = $pjtid;
} else {
    $db->joinquery("DELETE FROM projects_products WHERE projectid='" . $_POST['projectid'] . "'");
    $db->joinquery("UPDATE location_projects SET project_name='" . $_POST['pjtname'] . "',project_date='" . $_POST['pjtdate'] . "' WHERE projectid='" . $_POST['projectid'] . "'");
    $inspjt = $_POST['projectid'];
}
$db->joinquery("UPDATE location SET projectid=" . $inspjt . " WHERE locationid='" . $_POST['locationid'] . "'");
for ($i = 0; $i < count($_POST['data']); $i++) {
    $exp_pdt = explode('@', $_POST['data'][$i]);
    if ($exp_pdt[0] != "") {
        $db->joinquery("UPDATE window SET selected_product='" . $exp_pdt[2] . "',selected_price='" . $exp_pdt[0] . "',selected_hours='" . $exp_pdt[1] . "' WHERE windowid='" . $exp_pdt[3] . "'");
        $db->joinquery("UPDATE window SET projectid='" . $inspjt . "' WHERE windowid='" . $exp_pdt[3] . "'");
        $ins_pdts = array(
            'locationid' => $_POST['locationid'],
            'projectid'     => $inspjt,
            'windowid' => $exp_pdt[3],
            'product' => $exp_pdt[2]
        );
        $inspdts = $db->ins_rec("projects_products", $ins_pdts);
        $del_ledger = $db->joinquery("DELETE FROM Ledger WHERE windowid='" . $exp_pdt[3] . "'");
        if ($exp_pdt[2] != 'HOLD') {
            $promotinalvalu = 0;
            $discount = (100 - $promotinalvalu);
            $getmargins = $db->joinquery("SELECT * FROM location_margins WHERE locationid=" . $_POST['locationid'] . "");
            $getparams = $db->joinquery("SELECT * FROM params");
            if (mysqli_num_rows($getmargins) > 0) {
                $margin = mysqli_fetch_array($getmargins);
                $evsmargin = $margin['evsmargin'];
                $igumargin = $margin['igumargin'];
                $labourrate = $margin['labourrate'];
                $travelrate = $marin['travelrate'];
                $rowparam = mysqli_fetch_array($getparams);
                $evsmaterials = $rowparam['evsmaterials'];
                $dgmaterials = $rowparam['dgmaterials'];
                $igumaterials = $rowparam['igumaterials'];
            } else {
                mysqli_data_seek($getparams, 0);
                $rowparam = mysqli_fetch_array($getparams);
                $evsmargin = $evsmaterials = $rowparam['evsmaterials'];
                $dgmaterials = $rowparam['dgmaterials'];
                $igumargin = $igumaterials = $rowparam['igumaterials'];
                $labourrate = $rowparam['labourrate'];
                $travelrate = $rowparam['travelrate'];
            }
            $getCode = $db->joinquery("SELECT panel.panelid,panel.windowid,panel.`glasstypeid`,panel.width,panel.`height`,panel.dglabour AS IGUlabour,panel.evslabour AS EVSlabour,panel.`safetyid`,paneloption_glasstype.name AS glassname,paneloption_safety.name AS saftyname, paneloption_safety.safetyvalue,paneloption_style.evsGlassX,paneloption_style.evsGlassY,paneloption_style.retroGlassX,paneloption_style.retroGlassY,paneloption_style.IGUassemble,paneloption_style.EVSassemble FROM `panel`,paneloption_glasstype,paneloption_safety,paneloption_style WHERE panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.safetyid=paneloption_safety.safetyid AND panel.styleid=paneloption_style.styleid AND panel.windowid='" . $exp_pdt[3] . "'");
            while ($rowcode = mysqli_fetch_array($getCode)) {
                if ($exp_pdt[2] == "EVSx2" || $exp_pdt[2] == "EVSx3") {
                    $glassX = $rowcode['evsGlassX'];
                    $glassY = $rowcode['evsGlassY'];
                    if ($exp_pdt[2] == "EVSx2") {
                        $productid = 4;
                        $glassrate = $rowparam['evsx2glassrate'];
                    }
                    if ($exp_pdt[2] == "EVSx3") {
                        $productid = 5;
                        $glassrate = $rowparam['evsx3glassrate'];
                    }
                    $marginvalue = $evsmargin;
                    $m2 = round((((($rowcode['width'] + $glassX) * ($rowcode['height'] + $glassY)) * 0.000001)), 2);
                    $lm = round((((($rowcode['width'] + 72) + ($rowcode['height'] + 72)) * 2) * 0.001), 2);
                    $wsvalue_panel = round((($evsmaterials * $lm) + ($rowcode['EVSassemble'] / $labourrate)), 2);
                    $rrpvalue_panel = round((($evsmaterials * $lm) + ($rowcode['EVSassemble'] / $labourrate) * $evsmargin), 2);
                    $rate_panel = round(($rrpvalue_panel / $m2), 2);
                    $quantity_install = $rowcode['EVSlabour'];
                    $size_install = $rowcode['EVSlabour'];
                    $name_install = "Install EVS";
                    $shortname_install = "Install EVS";
                    $wsvalue_install = round(((($labourrate * $rowcode['EVSlabour'])) * $promotinalvalu), 2);
                    $rate_install = $rowcode['EVSlabour'] != 0 ? round(($wsvalue_install / $rowcode['EVSlabour']), 2) : 0;
                } else {
                    $glassX = $rowcode['retroGlassX'];
                    $glassY = $rowcode['retroGlassY'];
                    if ($exp_pdt[2] == "SGU" || $exp_pdt[2] == "SDG") {
                        $productid = 1;
                        $glassrate = $rowparam['sguglassrate'];
                    }
                    if ($exp_pdt[2] == "IGUX2" || $exp_pdt[2] == "MAXe") {
                        $productid = 2;
                        $glassrate = $rowparam['igux2glassrate'];
                    }
                    if ($exp_pdt[2] == "IGUX3" || $exp_pdt[2] == "XCLe") {
                        $productid = 3;
                        $glassrate = $rowparam['igux3glassrate'];
                    }
                    $marginvalue = $igumargin;
                    $m2 = round((((($rowcode['width'] + $glassX) * ($rowcode['height'] + $glassY)) * 0.000001)), 2);
                    $lm = round((((($rowcode['width'] + 72) + ($rowcode['height'] + 72)) * 2) * 0.001), 2);
                    $wsvalue_panel = round((($igumaterials * $lm) + ($rowcode['IGUassemble'] / $labourrate)), 2);
                    $rrpvalue_panel = round((($igumaterials * $lm) + ($rowcode['IGUassemble'] / $labourrate) * $igumargin), 2);
                    $rate_panel = $m2 != 0 ? round(($rrpvalue_panel / $m2), 2) : 0;
                    $quantity_install = $rowcode['IGUlabour'];
                    $size_install = $rowcode['IGUlabour'];
                    $name_install = "Install IGU";
                    $shortname_install = "Install IGU";
                    $wsvalue_install = round(((($labourrate * $rowcode['IGUlabour'])) * $promotinalvalu), 2);
                    $rate_install = round(($wsvalue_install / $rowcode['IGUlabour']), 2);
                }
                $quantity = $lm;
                # cal glasscost
                $glasscost_name = $exp_pdt[2] . "+" . $rowcode['glassname'];
                $glasscost_shortname = $exp_pdt[2] . "+" . $rowcode['glassname'];
                $glasscost_description = $exp_pdt[2] . "+" . $rowcode['glassname'] . "+" . $rowcode['saftyname'] . "+" . $rowcode['width'] . "+ wx" . "+" . $rowcode['height'] . "+h mm";
                $glasscost_wsvalue = round((($glassrate + ($rowcode['safetyvalue'] / 2)) * $m2), 2);
                $glasscost_rrpvalue = round(((($glassrate + ($rowcode['safetyvalue'] / 2)) * $m2) * ($evsmargin)), 2);
                $glasscost_rate = $m2 != 0 ? round(($glasscost_rrpvalue / $m2), 2) : 0;
                $db->ins_rec('Ledger', array(
                    'agentid' => $_SESSION['agentid'],
                    'locationid' => $_POST['locationid'],
                    'windowid' => $exp_pdt[3],
                    'panelid' => $rowcode['panelid'],
                    'roomid' => $exp_pdt[5],
                    'productid' => $productid,
                    'unittag' => 'm2',
                    'size' => $m2,
                    'margin' => $marginvalue,
                    'quantity' => $quantity,
                    'isQuoted' => 1,
                    'discount' => $discount,
                    'type' => 'Glass',
                    'code' => $rowcode['glassname'],
                    'name' => $glasscost_name,
                    'shortname' => $glasscost_shortname,
                    'description' => $glasscost_description,
                    'wsvalue' => $glasscost_wsvalue,
                    'rrpvalue' => $glasscost_rrpvalue,
                    'rate' => is_infinite($glasscost_rate) ? 0 : $glasscost_rate
                ));
                #cal panelcost
                $description_panel = $exp_pdt[2] . "+" . $row_type['name'] . "+" . $rowcode['width'] . "+ ‘w x’ +" . $rowcode['height'] . "+ h mm";
                $db->ins_rec('Ledger', array(
                    'agentid' => $_SESSION['agentid'],
                    'locationid' => $_POST['locationid'],
                    'windowid' => $exp_pdt[3],
                    'panelid' => $rowcode['panelid'],
                    'roomid' => $exp_pdt[5],
                    'productid' => $productid,
                    'unittag' => 'unit',
                    'size' => $m2,
                    'margin' => $marginvalue,
                    'quantity' => $quantity,
                    'isQuoted' => 1,
                    'discount' => $discount,
                    'type' => 'Panel',
                    'code' => $exp_pdt[2],
                    'name' => $exp_pdt[2],
                    'shortname' => $exp_pdt[2],
                    'description' => $glasscost_description,
                    'wsvalue' => $wsvalue_panel,
                    'rrpvalue' => $rrpvalue_panel,
                    'rate' => is_infinite($rate_panel) ? 0 : $rate_panel
                ));
                # install cost
                $description_install = "Install " . $exp_pdt[2];
                $rrpvalue_install = $wsvalue_install;
                $db->ins_rec('Ledger', array(
                    'agentid' => $_SESSION['agentid'],
                    'locationid' => $_POST['locationid'],
                    'windowid' => $exp_pdt[3],
                    'panelid' => $rowcode['panelid'],
                    'roomid' => $exp_pdt[5],
                    'productid' => $productid,
                    'unittag' => 'h',
                    'size' => $size_install,
                    'margin' => 0,
                    'quantity' => $quantity_install,
                    'isQuoted' => 1,
                    'discount' => $discount,
                    'type' => 'Install',
                    'code' => 'Install',
                    'name' => $name_install,
                    'shortname' => $shortname_install,
                    'description' => $description_install,
                    'wsvalue' => $wsvalue_install,
                    'rrpvalue' => $rrpvalue_install,
                    'rate' => is_infinite($rate_install) ? 0 : $rate_install
                ));
                #cal extracost
                $getprodcut = $db->joinquery("SELECT * FROM products WHERE productid='$productid'");
                $rowprodcut = mysqli_fetch_array($getprodcut);
                if ($rowprodcut['sizemode'] == 'lm') {
                    $size_product = $lm;
                } else {
                    $size_product = $m2;
                }
                $wsvalue_pdt = round(($rowprodcut['wsvalue'] + ($rowprodcut['hours'] * $labourrate)), 2);
                $rrpvalue_Pdt = round(($rowprodcut['rrpvalue'] + ($rowprodcut['hours'] * $labourrate)), 2);
                if ($exp_pdt[2] == "EVSx2" || $exp_pdt[2] == "EVSx3") {
                    $rate_pdt = $rowcode['EVSlabour'] != 0 ? round(($rrpvalue_Pdt / $rowcode['EVSlabour']), 2) : 0;
                } else {
                    $rate_pdt = $rowcode['IGUlabour'] != 0 ? round(($rrpvalue_Pdt / $rowcode['IGUlabour']), 2) : 0;
                }
                $db->ins_rec('Ledger', array(
                    'agentid' => $_SESSION['agentid'],
                    'locationid' => $_POST['locationid'],
                    'windowid' => $exp_pdt[3],
                    'panelid' => $rowcode['panelid'],
                    'roomid' => $exp_pdt[5],
                    'productid' => $productid,
                    'unittag' => $rowprodcut['unittag'],
                    'size' => $size_product,
                    'margin' => 0,
                    'quantity' => $lm,
                    'isQuoted' => 1,
                    'discount' => $discount,
                    'type' => 'Product',
                    'code' => $rowprodcut['code'],
                    'name' => $rowprodcut['name'],
                    'shortname' => $rowprodcut['shortname'],
                    'description' => $rowprodcut['description'],
                    'wsvalue' => $wsvalue_pdt,
                    'rrpvalue' => $rrpvalue_Pdt,
                    'rate' => is_infinite($rate_pdt) ? 0 : $rate_pdt
                ));
            }
        }
        //h	old
    }
}
/* layer begins*/
$gethourrate = $db->joinquery("SELECT labourrate FROM agent WHERE agentid='" . $_SESSION['agentid'] . "'");
$rowhourrate = mysqli_fetch_array($gethourrate);
$labourrate = $rowhourrate['labourrate'];
$Locationid = $_POST['locationid'];
$getglassids = $db->joinquery("SELECT DISTINCT(panel.glasstypeid) FROM panel,window,room,paneloption_glasstype,location WHERE window.roomid=room.roomid AND room.locationid=location.locationid AND panel.windowid=window.windowid AND window.selected_product!='Hold' AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND location.locationid=" . $_POST['locationid'] . "");
while ($rowglass = mysqli_fetch_array($getglassids)) {
    $glasstypeids[] = $rowglass['glasstypeid'];
}
if (count($glasstypeids) > 0) {
    $resids = array_unique($glasstypeids);
    $glassidjoin = join(',', $resids);
    $getlayerCount = $db->joinquery("SELECT * FROM window_layers WHERE locationid='" . $_POST['locationid'] . "'");
    if (mysqli_num_rows($getlayerCount) == 0) {
        $getglasstype = $db->joinquery("SELECT * FROM paneloption_glasstype WHERE glasstypeid IN($glassidjoin)");
        while ($rowglass = mysqli_fetch_assoc($getglasstype)) {
            $getlayers = $db->joinquery("SELECT `layersid`,`icon`,`name`,`glassType`,`outsideGlasstype`,`outsideThickness`,`spacerColor`,`sapcerWidth`,`insideGlasstype`,`insideThickness` FROM `paneloption_layers` WHERE glassType='" . $rowglass['glasstypeid'] . "' ORDER BY layersid ASC");
            $layers = array();
            $i = 0;
            while ($row_layers = mysqli_fetch_assoc($getlayers)) {
                $post['layer_id'] = $row_layers['layersid'];
                $post['name'] = $row_layers['name'];
                $layers[] = $post;
                $glasstypeid[] = $row_layers['glassType'];
                if ($i == 0) {
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
            //$			rowglass['glasstypeid '] =join(',',$glasstypeid);
            $Glass[] = $rowglass;
        }
    } else {
        $getglasstype1 = $db->joinquery("SELECT * FROM paneloption_glasstype WHERE glasstypeid IN($glassidjoin)");
        while ($rowglass1 = mysqli_fetch_assoc($getglasstype1)) {
            $windowlayerdetails = $db->joinquery("SELECT paneloption_layers.* FROM paneloption_layers,window_layers WHERE paneloption_layers.layersid=window_layers.layerid AND paneloption_layers.glassType=window_layers.glassid AND window_layers.glassid='" . $rowglass1['glasstypeid'] . "' AND window_layers.locationid='" . $Locationid . "'");
            $layers1 = array();
            $post2 = array();
            $post3  = array();
            if (mysqli_num_rows($windowlayerdetails) > 0) {
                while ($row_layes = mysqli_fetch_assoc($windowlayerdetails)) {
                    $post1['layer_id'] = $row_layes['layersid'];
                    $post1['name'] = $row_layes['name'];
                    $getremaining_layers = $db->joinquery("SELECT `layersid` AS layer_id,`name` FROM `paneloption_layers` WHERE glassType='" . $rowglass1['glasstypeid'] . "' AND layersid!='" . $row_layes['layersid'] . "'");
                    while ($rowremiang = mysqli_fetch_assoc($getremaining_layers)) {
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
            } else {
                /* selecting the layer which was not in window_layer but in glass type ..  may be  updating the panel glass type*/
                $getlayers2 = $db->joinquery("SELECT `layersid`,`icon`,`name`,`glassType`,`outsideGlasstype`,`outsideThickness`,`spacerColor`,`sapcerWidth`,`insideGlasstype`,`insideThickness` FROM `paneloption_layers` WHERE glassType='" . $rowglass1['glasstypeid'] . "' ORDER BY layersid ASC");
                $layers2 = array();
                $k = 0;
                while ($row_layers2 = mysqli_fetch_assoc($getlayers2)) {
                    $post3['layer_id'] = $row_layers2['layersid'];
                    $post3['name'] = $row_layers2['name'];
                    $layers2[] = $post3;
                    $glasstypeid2[] = $row_layers2['glassType'];
                    if ($k == 0) {
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
} else {
    $Glass = array();
}
/* end layer*/
/* paint specifications*/
$panelcount = array();
$timecount = array();
$costcount = array();
$totalcostcount = array();
$getcolourids = $db->joinquery("SELECT colourid FROM panel,window,room,paneloption_glasstype,location WHERE window.roomid=room.roomid AND room.locationid=location.locationid AND panel.windowid=window.windowid AND window.selected_product!='Hold' AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND location.locationid=" . $_POST['locationid'] . "");
while ($rowcolours = mysqli_fetch_array($getcolourids)) {
    $colourid[] = $rowcolours['colourid'];
}
if (count($colourid) > 0) {
    $replicated = array_count_values($colourid);
    $uniquecolor = array_unique($colourid);
    $joincolor = join(',', $uniquecolor);
    $getcolour = $db->joinquery("SELECT `colourid`,`colourname`,`colorcode` FROM colours WHERE colourid IN($joincolor)");
    if (mysqli_num_rows($getcolour) > 0) {
        while ($rowcolor = mysqli_fetch_assoc($getcolour)) {
            $rowcolor['countpanels'] = $replicated[$rowcolor['colourid']];
            $getspeific = $db->joinquery("SELECT * FROM paint_specifications WHERE locationid='" . $Locationid . "' AND colourid='" . $rowcolor['colourid'] . "'");
            if (mysqli_num_rows($getspeific) > 0) {
                while ($row_paint = mysqli_fetch_array($getspeific)) {
                    if ($row_paint['selected_status'] == 1) {
                        $panelcount[] = $rowcolor['countpanels'];
                        $timecount[] = $row_paint['times'];
                        $costcount[] = $row_paint['cost'];
                        $totalcostcount[] = $row_paint['totalcost'];
                    }
                    $rowcolor['times'] = $row_paint['times'];
                    $rowcolor['hoursperpanel'] = $row_paint['hoursperpanel'];
                    $rowcolor['costperpanel'] = $row_paint['costperpanel'];
                    $rowcolor['cost'] = $row_paint['cost'];
                    $rowcolor['total'] = $row_paint['totalcost'];
                    $rowcolor['selected'] = $row_paint['selected_status'];
                }
            } else {
                $rowcolor['times'] = 0.0;
                $rowcolor['hoursperpanel'] = 0.0;
                $rowcolor['costperpanel'] = 0.0;
                $rowcolor['cost'] = 0.0;
                $rowcolor['total'] = 0.0;
                $rowcolor['selected'] = 0;
            }
            $paints[] = $rowcolor;
            if (count($panelcount) > 0)
                $total_panelcount = array_sum($panelcount);
            else
                $total_panelcount = 0;
            if (count($timecount) > 0)
                $total_timecount = array_sum($timecount);
            else
                $total_timecount = 0;
            if (count($costcount) > 0)
                $total_costcount = array_sum($costcount);
            else
                $total_costcount = 0;
            if (count($totalcostcount) > 0)
                $total_totalcostcount = array_sum($totalcostcount);
            else
                $total_totalcostcount = 0;
        }
    }
}
/* end paint specifications*/
$vardate = date('d-m-Y', strtotime($_POST['pjtdate']));
$datedisp = date('Y-m-d', strtotime($_POST['pjtdate']));
?>
<!-- layer-->
<input type="hidden" id="ajx_pjtid" value="<?php echo $inspjt; ?>">
<input type="hidden" id="ajx_pjtname" value="<?php echo $_POST['pjtname'] . " " . $vardate; ?>">
<input type="hidden" id="ajx_edit_pjtname" value="<?php echo $_POST['pjtname']; ?>">
<input type="hidden" id="ajx_pjtdate" value="<?php echo $datedisp; ?>">
<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="glass-layers">
                    <div class="table-responsive">
                        <table class="table cust-table cust-two-table">
                            <thead>
                                <tr>
                                    <th>Glass Specification</th>
                                    <th></th>
                                    <th>Composition</th>
                                    <th>Outside</th>
                                    <th class="text-center">mm</th>
                                    <th>Spacer</th>
                                    <th class="text-center">mm</th>
                                    <th>Colour</th>
                                    <th>Inside</th>
                                    <th class="text-center">mm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($Glass as $valglass) { ?>
                                    <tr>
                                        <td><?php echo $valglass['name']; ?></td>
                                        <td id="icon<?php echo $valglass['glasstypeid']; ?>"> <?php if ($valglass['layerdetails']['icon'] != '') { ?><img src="<?php echo $gLayerURL; ?><?php echo $valglass['layerdetails']['icon']; ?>" width="100px">
                                            <?php } ?></td>
                                        <td>
                                            <select class="form-control layer-composite" data-id="<?php echo $valglass['glasstypeid'];
                                                                                                    ?>" data-location="<?php echo $_POST['locationid'];
                                                                                                                        ?>" name="layercomposite1[]">
                                                <!-- <option>4/6bk/4 Standard</option>-->
                                                <?php foreach ($valglass['layercomposite'] as $valcomposite) {
                                                    echo '<option value="' . $valcomposite['layer_id'] . '@' . $valglass['glasstypeid'] . '">' . $valcomposite['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td id="outsidetype<?php echo $valglass['glasstypeid'];
                                                            ?>"><?php echo $valglass['layerdetails']['outsideGlasstype'];
                                                                ?></td>
                                        <td id="outsidethickness<?php echo $valglass['glasstypeid'];
                                                                ?>"><?php echo $valglass['layerdetails']['outsideThickness'];
                                                                    ?></td>
                                        <td id="spacer<?php echo $valglass['glasstypeid'];
                                                        ?>"><?php echo $valglass['layerdetails']['spacer'];
                                                            ?></td>
                                        <td align="center" id="sapcerWidth<?php echo $valglass['glasstypeid'];
                                                                            ?>"><?php echo $valglass['layerdetails']['sapcerWidth'];
                                                                                ?></td>
                                        <td>
                                            <div class="colour-box"><span style="color:#FFF;background-color:#<?php echo $valglass['layerdetails']['colorcode'];
                                                                                                                ?>" id="colorcode<?php echo $valglass['glasstypeid'];
                                                                                                                                    ?>"><?php echo $valglass['layerdetails']['short_spacer'];
                        ?></span></div>
                                        </td>
                                        <td id="insidetype<?php echo $valglass['glasstypeid'];
                                                            ?>"><?php echo $valglass['layerdetails']['insideGlasstype'];
                                                                ?></td>
                                        <td align="center" id="insidethickness<?php echo $valglass['glasstypeid'];
                                                                                ?>"><?php echo $valglass['layerdetails']['insideThickness'];
                                                                                    ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <table class="table cust-table cust-two-table nn-cust-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Paint Specification</th>
                                    <th></th>
                                    <th>Paint Name</th>
                                    <th>#Pans</th>
                                    <th width="120">Hours p/pan</th>
                                    <th>Time</th>
                                    <th width="120">$p/pan</th>
                                    <th>Costs</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($paints[0])) {
                                    foreach ($paints as $valpaint) {
                                ?>
                                        <tr>
                                            <td class="ajax_paintextra">Add as Extra </td>
                                            <td><input type="checkbox" name="paintrad" class="radpaint" data-locationid="<?php echo $Locationid; ?>" id="paintrad<?php echo $valpaint['colourid']; ?>" data-colourid="<?php echo $valpaint['colourid']; ?>" <?php if ($valpaint['selected'] == 1) { ?> checked=="checked" <?php } ?>></td>
                                            <td>
                                                <div class="colour-box"><span style="height:30px;background-color:#<?php echo $valpaint['colorcode']; ?>"></span></div>
                                            </td>
                                            <td><?php echo $valpaint['colourname']; ?></td>
                                            <td id="cntpans<?php echo $valpaint['colourid']; ?>" <?php if ($valpaint['selected'] == 1) { ?> class="testcountpanels1" <?php } ?>><?php echo $valpaint['countpanels']; ?></td>
                                            <td><input type="text" class="form-control hoursperpanel" id="hoursperpanel<?php echo $valpaint['colourid']; ?>" data-locationid="<?php echo $Locationid; ?>" data-colourid="<?php echo $valpaint['colourid']; ?>" data-labourrate="<?php echo $labourrate; ?>" value="<?php echo $valpaint['hoursperpanel']; ?>"></td>
                                            <td id="time<?php echo $valpaint['colourid']; ?>" <?php if ($valpaint['selected'] == 1) { ?> class="testtimes1" <?php } ?>><?php echo $valpaint['times']; ?></td>
                                            <td>
                                                <div style="display: flex; align-items: center;">
                                                    $<input type="text" class="form-control costperpanel" id="costperpanel<?php echo $valpaint['colourid']; ?>" data-locationid="<?php echo $Locationid; ?>" data-colourid="<?php echo $valpaint['colourid']; ?>" data-labourrate="<?php echo $labourrate; ?>" value="<?php echo $valpaint['costperpanel']; ?>" style="margin-left: 10px;">
                                                </div>
                                            </td>
                                            <td>$<span id="costtd<?php echo $valpaint['colourid']; ?>" <?php if ($valpaint['selected'] == 1) { ?> class="testcost1" <?php } ?>><?php echo $valpaint['cost']; ?></span></td>
                                            <td>$<span id="totaltd<?php echo $valpaint['colourid']; ?>" <?php if ($valpaint['selected'] == 1) { ?> class="testtotal1" <?php } ?>><?php echo $valpaint['total']; ?></span></td>
                                        </tr>
                                <?php }
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <th>Totals</th>
                                    <td class="td_paint" id="total_panelscount"><?php echo $total_panelcount; ?></td>
                                    <td class="td_paint"></td>
                                    <td class="td_paint" id="total_timecount"><?php echo $total_timecount; ?></td>
                                    <td class="td_paint"></td>
                                    <td class="td_paint" id="total_costcount">$ <?php echo $total_costcount; ?></td>
                                    <td class="td_paint" id="total_totalscount">$ <?php echo $total_totalcostcount; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end layer-->
<?php
$get_window = $db->joinquery("SELECT room.`roomid`,room.`name` AS room_name,window.notes,window.extras,window.windowid,window.windowtypeid,window_type.numpanels,window_type.name,window.selected_product FROM room,window,window_type WHERE room.roomid=window.roomid AND window.windowtypeid=window_type.windowtypeid AND  room.locationid=" . $_POST['locationid'] . " ORDER BY room_name ASC");
if (mysqli_num_rows($get_window) > 0) {
    $i = 64;
    while ($row_pann = mysqli_fetch_array($get_window)) {
        $i = $i + 1;
        if ($row_pann['selected_product'] != 'HOLD') {
?>
            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel_info_main">
                                <div class="window_sets">
                                    <h4 class="main_title">Window Set [<?php echo chr($i);
                                                                        ?>]</h4>
                                    <p><?php echo $row_pann['room_name'];
                                        ?> <span><?php echo $row_pann['name'];
                                                    ?></span></p>
                                    <?php
                                    if ($row_pann['windowtypeid'] != 0) {
                                    ?>
                                        <a data-toggle="modal" data-target="#windowtypes" href="#" data-id="<?php echo $row_pann['windowid'];
                                                                                                            ?>@<?php echo $row_pann['numpanels'];
                                                                                                                ?>" class="window-images" id="windowtype<?php echo $row_pann['windowid'];
                                            ?>"> <img src="<?php echo $gwindowURL . $row_pann['windowtypeid'];
                                                        ?>.png" class="img-responsive"></a>
                                    <?php
                                    }
                                    //$get_panel=$db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE panel.safetyid=paneloption_safety.safetyid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.`styleid`=paneloption_style.styleid AND panel.conditionid=paneloption_condition.conditionid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.windowid=".$row_pann['windowid']."");
                                    /* $get_panel = $db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.windowid=" . $row_pann['windowid'] . "");*/
                                    $get_panel = $db->joinquery("SELECT panel.panelid,panel.width,panel.height,panel.center,panel.measurement,panel.panelnum,panel.profileid,panel.windowid,panel.`safetyid`,panel.`glasstypeid`,panel.`styleid`,panel.`conditionid`,panel.`colourid`,panel.`astragalsid`,`paneloption_style`.name AS stylename,paneloption_style.`evsProfileTop`,paneloption_style.`evsProfileSides`,paneloption_style.`evsProfileBottom`,paneloption_style.`evsGlassX`,paneloption_style.`evsGlassY`,paneloption_style.`evsProfileX`,paneloption_style.`evsProfileY`,paneloption_style.`retroProfileTop`,paneloption_style.`retroProfileSides`,paneloption_style.`retroProfileBottom`,paneloption_style.`retroGlassX`,paneloption_style.`retroGlassY`,paneloption_style.`retroProfileX`,paneloption_style.`retroProfileY`,paneloption_style.evsProfileRight,paneloption_style.evsProfileLeft,paneloption_style.evsOutPanelThickness,paneloption_style.evsOutPanelType,paneloption_style.evsInPanelThickness,paneloption_style.evsInPanelType,paneloption_style.retroOutPanelThickness,paneloption_style.retroOutPanelType,paneloption_style.retroInPanelThickness,paneloption_style.retroInPanelType,paneloption_style.retroProfileLeft,paneloption_style.retroProfileRight,paneloption_astragal.name AS astragal_name,paneloption_condition.name AS condition_name,paneloption_safety.name AS safty_name,paneloption_glasstype.name AS galsstype_name,paneloption_glasstype.typevalue,colours.colourname,colours.colorcode FROM panel,paneloption_astragal,paneloption_safety,paneloption_style,paneloption_glasstype,paneloption_condition,colours WHERE 
panel.styleid=paneloption_style.styleid AND panel.safetyid=paneloption_safety.safetyid AND panel.astragalsid=paneloption_astragal.astragalsid AND panel.glasstypeid=paneloption_glasstype.glasstypeid AND panel.conditionid=paneloption_condition.conditionid AND panel.colourid = colours.colourid AND panel.windowid=" . $row_pann['windowid'] . "");
                                    ?>
                                </div>
                                <div class="window_info table_custom">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <h4 class="main_title">Panel Size</h4>
                                                    </th>
                                                    <!-- <th><h4 class="main_title">Profile</h4></th>-->
                                                    <th>
                                                        <h4 class="main_title">Style</h4>
                                                    </th>
                                                    <th>
                                                        <h4 class="main_title">Safety</h4>
                                                    </th>
                                                    <th>
                                                        <h4 class="main_title">Glass Type</h4>
                                                    </th>
                                                    <th>
                                                        <h4 class="main_title">Condition</h4>
                                                    </th>
                                                    <th>
                                                        <h4 class="main_title">Astrigals</h4>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (mysqli_num_rows($get_panel) > 0) {
                                                    while ($row_panel = mysqli_fetch_array($get_panel)) {
                                                        //$		getprofiles=$db->joinquery("SELECT * FROM profiles");
                                                ?>
                                                        <tr>
                                                            <td><span>Panel <?php echo $row_panel['panelnum'];
                                                                            ?></span> <span id="spn_width<?php echo $row_panel['panelid'];
                                                                                                            ?>" style="display:initial"><?php echo $row_panel['width'];
                                                            ?> x <?php echo $row_panel['height'];
                                    ?> x <?php echo $row_panel['center'];
                ?></span></td>
                                                            <td class="cust_slct">
                                                                <?php if ($row_panel['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir . $row_panel['styleid'] . ".png")) {
                                                                ?>
                                                                    <a data-toggle='modal' data-target='#styleModalprop' href='#' onClick="getVal(<?php echo $_POST['locationid'];
                                                                                                                                                    ?>,<?php echo $row_panel['panelid'];
                                                                                                                                                        ?>,<?php echo $row_panel['styleid'];
        ?>,'<?php echo $gPanelOptionsPhotoURL . $row_panel['styleid'];
        ?>.png','<?php echo $row_panel['stylename'];
                ?>')"><span id='span<?php echo $row_panel['panelid'];
                                ?>'><img src="<?php echo $gPanelOptionsPhotoURL . $row_panel['styleid'];
                                    ?>.png" class="img-responsive" style="width:50px; height:50px;"></span></a>
                                                                <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td class="cust_slct" id="td_safty<?php echo $row_panel['panelid'];
                                                                                                ?>"><?php echo $row_panel['safty_name'];
                                                                                                    ?></td>
                                                            <td class="cust_slct" id="td_glass<?php echo $row_panel['panelid'];
                                                                                                ?>"><?php echo $row_panel['galsstype_name'];
                                                                                                    ?></td>
                                                            <td class="cust_slct" id="td_condition<?php echo $row_panel['panelid'];
                                                                                                    ?>"><a data-toggle="modal" data-target="#myModalpaint" data-colourid="<?php echo $row_panel['colourid']; ?>" data-panelid="<?php echo $row_panel['panelid']; ?>" data-windowid="<?php echo $row_panel['windowid']; ?>" data-conditionid="<?php echo $row_panel['conditionid']; ?>" class="paintanchor">
                                                                    <div class="colour-box"><span style="color:#FFF; background-color:#<?php echo $row_panel['colorcode']; ?>" id="colorcode<?php echo $row_panel['panelid']; ?>"><?php echo $row_panel['condition_name']; ?></span></div>
                                                                </a></td>
                                                            <td class="cust_slct" id="td_astragal<?php echo $row_panel['panelid'];
                                                                                                    ?>"><?php echo $row_panel['astragal_name'];
                                                                                                        ?></td>
                                                        </tr>
                                                <?php
                                                        $glasstypeids[] = $row_panel['glasstypeid'];
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div><!-- ./col-lg-12 -->
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr style="color:#fff; background:#565759;">
                                            <th>ID</th>
                                            <th colspan="2">Cut List</th>
                                            <th colspan="4">Measurements</th>
                                            <th colspan="5">Glass</th>
                                            <th colspan="3">Top</th>
                                            <th colspan="3">Bottom</th>
                                            <th colspan="3">Sides(Left)</th>
                                            <th colspan="3">Sides(Right)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th></th>
                                            <th>Window[Panel]</th>
                                            <th>Style</th>
                                            <th>Safety</th>
                                            <th>Height</th>
                                            <th>Width</th>
                                            <th>Center</th>
                                            <th>(+w)</th>
                                            <th>W</th>
                                            <th>(+h)</th>
                                            <th>H</th>
                                            <th>m2</th>
                                            <th>Profile</th>
                                            <th>(+w)</th>
                                            <th>(W)</th>
                                            <th>Profile</th>
                                            <th>(+w)</th>
                                            <th>(W)</th>
                                            <th>Profile</th>
                                            <th>(+h)</th>
                                            <th>(H)</th>
                                            <th>Profile</th>
                                            <th>(+h)</th>
                                            <th>(H)</th>
                                        </tr>
                                        <?php
                                        mysqli_data_seek($get_panel, 0);
                                        if (mysqli_num_rows($get_panel) > 0) {
                                            $j = 0;
                                            while ($row_panel = mysqli_fetch_array($get_panel)) {
                                                $j++;
                                                if ($row_pann['selected_product'] == "EVSx3" || $row_pann['selected_product'] == "EVSx2") {
                                                    $profiletop = $row_panel['evsProfileTop'];
                                                    $profilebottom = $row_panel['evsProfileBottom'];
                                                    $leftprofile = $row_panel['evsProfileLeft'];
                                                    $rightprofile = $row_panel['evsProfileRight'];
                                                    $glassX = $row_panel['evsGlassX'];
                                                    $glassY = $row_panel['evsGlassY'];
                                                    $profileX = $row_panel['evsProfileX'];
                                                    $profileY = $row_panel['evsProfileY'];
                                                } else {
                                                    $profiletop = $row_panel['retroProfileTop'];
                                                    $profilebottom = $row_panel['retroProfileBottom'];
                                                    $profilesides = $row_panel['retroProfileSides'];
                                                    $leftprofile = $row_panel['retroProfileLeft'];
                                                    $rightprofile = $row_panel['retroProfileRight'];
                                                    $glassX = $row_panel['retroGlassX'];
                                                    $glassY = $row_panel['retroGlassY'];
                                                    $profileX = $row_panel['retroProfileX'];
                                                    $profileY = $row_panel['retroProfileY'];
                                                }
                                                if ($glassX == NULL) $glassX = 0;
                                                if ($glassY == NULL) $glassY = 0;
                                                if ($profileX == NULL) $profileX = 0;
                                                if ($profileY == NULL) $profileY = 0;
                                                if (($row_panel['center']) > ($row_panel['height'])) {
                                                    // 			$glassSizey=($row_panel['center'])+($row_panel['height']);
                                                    //$			profilesizey=($row_panel['center'])+($row_panel['height']);
                                                    $glassSizey = ($row_panel['center']) + $glassY;
                                                    $profilesizey = ($row_panel['center']) + $profileY;
                                                    $m2 = round(((($row_panel['width'] + $glassX) * ($row_panel['center'])) * 0.000001), 2);
                                                } else {
                                                    if ($row_panel['height'] > 0) {
                                                        $glassSizey = ($row_panel['height']) + ($glassY);
                                                        $profilesizey = ($row_panel['height']) + ($profileY);
                                                    } else {
                                                        $glassSizey = 0;
                                                        $profilesizey = 0;
                                                    }
                                                    $m2 = round(((($row_panel['width'] + $glassX) * ($row_panel['height'] + $glassY)) * 0.000001), 2);
                                                }
                                        ?>
                                                <tr>
                                                    <td><?php echo chr($i) . $j; ?></td>
                                                    <td><?php echo $row_pann['name'] . "[" . $row_panel['panelnum'] . "]"; ?></td>
                                                    <td><?php if ($row_panel['styleid'] > 0 && file_exists($gPanelOptionsPhotoDir . $row_panel['styleid'] . ".png")) {
                                                            echo "<img src=\"" . $gPanelOptionsPhotoURL . $row_panel['styleid'] . ".png?" . time() . "\" class=\"img-responsive\" style=\"width: 50px; height; 50px;\">";
                                                        }
                                                        ?></td>
                                                    <td><?php echo $row_panel['safty_name']; ?></td>
                                                    <td <?php if ($row_panel['measurement'] == 'estimate') { ?> style="color:#F00" <?php } ?>><?php echo $row_panel['height'];
                                                                                                                                                ?> </td>
                                                    <td <?php if ($row_panel['measurement'] == 'estimate') { ?> style="color:#F00" <?php } ?>><?php echo $row_panel['width'];
                                                                                                                                                ?> </td>
                                                    <td <?php if ($row_panel['measurement'] == 'estimate') { ?> style="color:#F00" <?php } ?>><?php echo $row_panel['center'];
                                                                                                                                                ?> </td>
                                                    <td><?php echo $glassX; ?></td>
                                                    <td><?php if ($row_panel['width'] > 0) {
                                                            echo ($row_panel['width'] + $glassX);
                                                        }
                                                        ?></td>
                                                    <td><?php echo $glassY; ?></td>
                                                    <td><?php echo $glassSizey; ?></td>
                                                    <td><?php echo $m2; ?></td>
                                                    <td>
                                                        <?php
                                                        if (file_exists($gProfilePhotoDir . $profiletop . ".png")) {
                                                        ?>
                                                            <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $profiletop;
                                                                                                ?>.png" style="color:blue;"><?php echo $profiletop;
                                                                                                                            ?></a></span>
                                                        <?php
                                                        } else {
                                                        ?><span><?php echo $profiletop;
                                                                ?></span><?php
                                                        }
                    ?>
                                                    </td>
                                                    <td><?php echo $profileX;
                                                        ?></td>
                                                    <td><?php if ($row_panel['width'] > 0) {
                                                            echo ($row_panel['width'] + $profileX);
                                                        }
                                                        ?></td>
                                                    <td> <?php
                                                            if (file_exists($gProfilePhotoDir . $profilebottom . ".png")) {
                                                            ?>
                                                            <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $profilebottom;
                                                                                                ?>.png" style="color:blue;"><?php echo $profilebottom;
                                                                                                                            ?></a></span>
                                                        <?php
                                                            } else {
                                                        ?><span><?php echo $profilebottom;
                                                                ?></span><?php
                                                            }
                    ?>
                                                    </td>
                                                    <td><?php echo $profileX;
                                                        ?></td>
                                                    <td><?php if ($row_panel['width'] > 0) {
                                                            echo ($row_panel['width'] + $profileX);
                                                        }
                                                        ?></td>
                                                    <td> <?php
                                                            if (file_exists($gProfilePhotoDir . $leftprofile . ".png")) {
                                                            ?>
                                                            <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $leftprofile; ?>.png" style="color:blue;"><?php echo $leftprofile;
                                                                                                                                                                        ?></a></span>
                                                        <?php
                                                            } else {
                                                        ?><span><?php echo $leftprofile;
                                                                ?></span><?php
                                                            }
                    ?>
                                                    </td>
                                                    <td><?php echo $profileY;
                                                        ?></td>
                                                    <td><?php echo $profilesizey;
                                                        ?></td>
                                                    <td> <?php
                                                            if (file_exists($gProfilePhotoDir . $rightprofile . ".png")) {
                                                            ?>
                                                            <span><a class="fs-gal" data-url="<?php echo $gProfilePhotoURL . $rightprofile; ?>.png" style="color:blue;"><?php echo $rightprofile;
                                                                                                                                                                        ?></a></span>
                                                        <?php
                                                            } else {
                                                        ?><span><?php echo $rightprofile;
                                                                ?></span><?php
                                                            }
                    ?>
                                                    </td>
                                                    <td><?php echo $profileY;
                                                        ?></td>
                                                    <td><?php echo $profilesizey;
                                                        ?></td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="photos" id="div_befor_img<?php echo $row_pann['windowid'];
                                                                    ?>">
                                   <?php
                                $before_photos = $db->joinquery("SELECT photoid FROM window_photo WHERE windowid='" . $row_pann['windowid'] . "'");
                                $beforCnt = mysqli_num_rows($before_photos);
                                ?>                                  
                                <h4 class="main_title_2 main-title-flex">Before Photos <?php  if($beforCnt!=0){?> <a  href="javascript:void(0)" onclick="getwindowList('<?php echo $row_pann[windowid];?>','before')"><i class="fa fa-arrow-right" aria-hidden="true"  style="color: #00adef;"></i></a><?php } ?><a class="plus-circle-blue" id="before-photo" data-id="<?php echo $row_pann['windowid'];
                                                                                                                                                ?>" data-toggle="modal" data-target="#myModalBefore" href="javascript:void(0)"><i class="fa fa-plus"></i></a></h4>
                                <!--<h4 class="main_title_2">Before Photos</h4>-->
                                <ul>
                                    <?php
                                    
                                    while ($row_phot = mysqli_fetch_array($before_photos)) {
                                    ?>
                                        <li> <img src="http://evsapp.nz/photos/<?php echo $row_phot['photoid'];
                                                                                ?>.jpg" class="fs-gal" data-url="http://evsapp.nz/photos/<?php echo $row_phot['photoid'];
                                                                                                                                            ?>.jpg"> </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div><!-- ./photos -->
                        </div><!-- ./col-lg-12 -->
                        <div class="col-lg-12">
                            <div class="notes">
                                <h4 class="main_title_2">Notes <a data-toggle="modal" data-target="#myModal<?php echo $row_pann['windowid'];
                                                                                                            ?>" href="#" data-id="<?php echo $row_pann['windowid'];
                                                                                                                                    ?>" class="btn btn-primary" style="margin-left:100px;">EDIT</a></h4>
                                <p id="notes<?php echo $row_pann['windowid'];
                                            ?>"><?php echo $row_pann['notes'];
                                                ?></p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="notes">
                                <h4 class="main_title_2">Extras <a data-toggle="modal" data-target="#AddExtra" href="#" data-id="<?php echo $row_pann['windowid'];
                                                                                                                                    ?>" class="btn btn-primary" style="margin-left:100px;" id="anchor_extra_view">ADD</a></h4>
                                <div class="table-responsive">
                                    <span id="view_<?php echo $row_pann['windowid'];
                                                    ?>">
                                        <?php
                                        $get_extras = $db->joinquery("SELECT window_extras.*,products.* FROM window_extras,products WHERE window_extras.productid=products.productid AND window_extras.windowid='" . $row_pann['windowid'] . "'");
                                        if (mysqli_num_rows($get_extras) > 0) {
                                        ?>
                                            <table class="table">
                                                <tr>
                                                    <td></td>
                                                    <td>Product</td>
                                                    <td>Cost</td>
                                                    <td>Quantity</td>
                                                    <td>Unit</td>
                                                    <td>Hour</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td style="border:none"><input type="hidden" name="page-status" value="<?php echo $_POST['pagestatus'];
                                                                                                                            ?>" /></td>
                                                </tr>
                                                <?php
                                                while ($row_extras = mysqli_fetch_array($get_extras)) {
                                                    $hours = round(($row_extras['hours'] * $row_extras['sizevalue']), 2);
                                                    if ($row_extras['imageid'] == 0) $row_extras['imageid'] = $row_extras['productid'];
                                                ?>
                                                    <tr>
                                                        <?php
                                                        if (file_exists($gSupplierProdcutsDir . $row_extras['imageid'] . ".png")) {
                                                            echo '<td><img src="' . $gSupplierProdcutsURL . $row_extras['imageid'] . '.png' . '" style="width:20px"></td>';
                                                        } else {
                                                            echo '<td></td>';
                                                        }
                                                        ?>
                                                        <td><?php echo $row_extras['name'];
                                                            ?></td>
                                                        <td>$<?php echo $row_extras['cost'];
                                                                ?> </td>
                                                        <td><?php echo $row_extras['quantity'];
                                                            ?></td>
                                                        <td><?php echo $row_extras['unitname'];
                                                            ?></td>
                                                        <td><?php echo $hours;
                                                            ?>&nbsp;Hours</td>
                                                        <td><a href="<?php echo $row_extras['linkURL'];
                                                                        ?>" target="_blank">More Info</a></td>
                                                        <td><a href="javascript:void(0)" id="delete-extra" onClick="delview(<?php echo $row_extras['extraid'];
                                                                                                                            ?>,<?php echo $row_extras['windowid'];
                                                                                                                                ?> )"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                        <td><a data-toggle="modal" data-target="#<?php echo $popupid;
                                                                                                    ?>" href="#" data-id="<?php echo $row_extras['extraid'];
                                                                                                                            ?>" id="edit-view-manager"><i class="fa fa-edit" aria-hidden="true"></i></a></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </table>
                                        <?php
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-lg-12">
                	<div class="notes">
                        <h4 class="main_title">Hazards</h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent at sapien est. Nunc ligula nibh, dictum a malesuada quis, venenatis nec tellus. Integer eget venenatis ligula. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec tellus neque, suscipit in augue quis, rhoncus tincidunt metus. Integer non hendrerit nisl.</p>
                    </div>
                </div>-->
                        <div class="col-lg-12">
                            <div class="photos" id="div_after_img<?php echo $row_pann['windowid'];
                                                                    ?>">
                                <?php
                                 $after_photos = $db->joinquery("SELECT photoid FROM window_after_photo WHERE windowid='" . $row_pann['windowid'] . "'");
                                 $afterCnt = mysqli_num_rows($after_photos);
                                 ?>
                                <h4 class="main_title_2 main-title-flex">After Photos <?php  if($afterCnt!=0){?><a  href="javascript:void(0)" onclick="getwindowList('<?php echo $row_pann[windowid];?>','after')"><i class="fa fa-arrow-right" aria-hidden="true"  style="color: #00adef;"></i></a><?php } ?><a class="plus-circle-blue" id="after-photo" data-id="<?php echo $row_pann['windowid'];
                                                                                                                                            ?>" data-toggle="modal" data-target="#myModalAfter" href="javascript:void(0)"><i class="fa fa-plus"></i></a></h4>
                                <ul>
                                    <?php
                                   
                                    while ($row_phot_after = mysqli_fetch_array($after_photos)) {
                                        if ($row_phot['photoid'] > 0 && file_exists($gPhotoDir . $row_phot['photoid'] . ".png")) ?>
                                        <li> <img src="<?php echo $gPhotoURL . $row_phot_after['photoid'];
                                                        ?>.jpg" class="fs-gal" data-url="<?php echo $gPhotoURL . $row_phot_after['photoid'];
                                                                                            ?>.jpg"> </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div id="myModal<?php echo $row_pann['windowid'];
                            ?>" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Add /Edit Window Notes</h4>
                        </div>
                        <div class="modal-body">
                            <p>
                            <table class="table">
                                <tr>
                                    <td><textarea name="notes" id="windownotes<?php echo $row_pann['windowid'];
                                                                                ?>" rows="10" style="resize:none; width:100%"><?php echo $row_pann['notes'];
                                                                                                                                ?></textarea></td>
                                </tr>
                                <tr>
                                    <td><input type="button" value="UPDATE" class="btn btn-primary" style="margin-left:50px;" onclick="updatenotes(<?php echo $row_pann['windowid'];
                                                                                                                                                    ?>,'notes')" /></td>
                                </tr>
                            </table>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="myModal1<?php echo $row_pann['windowid'];
                                ?>" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Add /Edit Window Extras</h4>
                        </div>
                        <div class="modal-body">
                            <p>
                            <table class="table">
                                <tr>
                                    <td><textarea name="notes" id="windowextras<?php echo $row_pann['windowid'];
                                                                                ?>" rows="10" style="resize:none; width:100%"><?php echo $row_pann['extras'];
                                                                                                                                ?></textarea></td>
                                </tr>
                                <tr>
                                    <td><input type="button" value="UPDATE" class="btn btn-primary" style="margin-left:50px;" onclick="updateextras(<?php echo $row_pann['windowid'];
                                                                                                                                                    ?>,'extras')" /></td>
                                </tr>
                            </table>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }
    }
}
?>