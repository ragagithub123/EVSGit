<?php ob_start();
session_start();
include ('../includes/functions.php');
if ($_POST['status'] == 1)
{

    $db->joinquery("DELETE FROM window_layers WHERE locationid='" . $_POST['locationid'] . "'");

    $glasstypeid = explode(',', $_POST['glassids']);

    $layerids = explode(',', $_POST['layerids']);

    for ($i = 0;$i < count($glasstypeid);$i++)
    {

        $db->joinquery("INSERT INTO window_layers(locationid,glassid,layerid)VALUES(" . $_POST['locationid'] . "," . $glasstypeid[$i] . "," . $layerids[$i] . ")");

    }
    $getlayers = $db->joinquery("SELECT `layersid`,`icon`,`name`,`glassType`,`outsideGlasstype`,`outsideThickness`,`spacerColor`,`sapcerWidth`,`insideGlasstype`,`insideThickness` FROM `paneloption_layers` WHERE layersid='" . $_POST['selectedlayerid'] . "'");
    $rowlayers = mysqli_fetch_assoc($getlayers);

    $getcolor = $db->joinquery("SELECT * FROM sapcercolor WHERE colourid ='" . $rowlayers['spacerColor'] . "'");

    $rowcolor = mysqli_fetch_array($getcolor);

    $splitup = explode(' ', $rowcolor['colourname']);

    $spcercolor = explode('(', $splitup[1]);

    $spacecolor = explode(')', $spcercolor[1]);

    $rowlayers['spacer'] = $spacecolor[0];

    $rowlayers['short_spacer'] = $splitup[0];

    $rowlayers['colorcode'] = $rowcolor['colorcode'];

    $insideglass = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $rowlayers['insideGlasstype'] . "'");
    $rowglassinside = mysqli_fetch_array($insideglass);
    $rowlayers['insideGlasstype'] = $rowglassinside['name'];

    $outsideglass = $db->joinquery("SELECT name FROM paneloption_glasstype WHERE glasstypeid='" . $rowlayers['outsideGlasstype'] . "'");
    $rowglassoutside = mysqli_fetch_array($outsideglass);

    $rowlayers['outsideGlasstype'] = $rowglassoutside['name'];

    $rowlayers['icon'] = $gLayerURL.$rowlayers['icon'];

    echo json_encode($rowlayers);

}



