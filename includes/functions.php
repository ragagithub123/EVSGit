<?php
include('database.php');
$db = new Database();

function sort_2d_desc($array, $key) {
    usort($array, function($a, $b) use ($key) {
        return strnatcasecmp($b[$key], $a[$key]);
    });

    return $array;
}
$Dwebsite="http://dash.evsapp.nz/";
//$Dwebsite="http://210.5.53.32/";
$DwebsiteDir="/var/www/vhosts/evsapp.nz/dash.EVSapp.nz/";

//$gWebsite = "https://newlive.evsapp.nz/";
 $gWebsite = "http://evsapp.nz/";
//$gWebsite = "https://210.5.53.32";
$gWebsiteDir = "/var/www/vhosts/evsapp.nz/httpdocs/";

$gPanelOptionsPhotoURL = $gWebsite."/assets/app/paneloptions/";
$gPanelOptionsPhotoDir = $gWebsiteDir. "/assets/app/paneloptions/";
$gProfilePhotoURL = $gWebsite."/assets/app/profiles/";
$gProfilePhotoDir = $gWebsiteDir. "/assets/app/profiles/";
$siteURL="https://dash.evsapp.nz";
//$siteURL="https://210.5.53.32";
$gPhotoDir=$gWebsiteDir."photos";

$gPhotoURL=$gWebsite."photos/";

$gAttachmentDir=$DwebsiteDir. "/assets/attachments/";
$gXmlDir = $DwebsiteDir. "/assets/xml/";
$gDownlaodurl=$Dwebsite."/assets/attachments/";

$DPhotoDir=$DwebsiteDir. "/assets/photos";

$DPhotoURL=$Dwebsite."assets/photos/";


$gwindowURL=$gWebsite."assets/app/windowtypes/";

$gPhotos=$gWebsite."photos/";

$gWindowOptionProductsDir=$gWebsiteDir. "/assets/products/";

$gWindowOptionProductsURL=$gWebsite."/assets/products/";

$gSignaturePhotoURL = $gWebsite."/assets/app/agents/";
$gSignaturePhotoDir = $gWebsiteDir. "assets/app/agents/";
$gLogoPhotoDir=$gWebsiteDir. "/assets/app/agents/logos/";
$gLogoPhotoURL=$gWebsite. "/assets/app/agents/logos/";
$gProductDir=$gWebsiteDir. "/assets/app/agents/products/";
$gProductURL=$gWebsite."/assets/app/agents/products/";
$gSupplierProdcutsDir=$gWebsiteDir."/assets/products/";
$gSupplierProdcutsURL=$gWebsite."/assets/products/";

$gquotepagePhotoDir = $gWebsiteDir. "/assets/app/agents/quotes/";
$gquotepagePhotoURL = $gWebsite."/assets/app/agents/quotes/";

$gTeamPhotoDir = $gWebsiteDir. "assets/app/agents/team/";

$gTeamPhotoURL = $gWebsite."/assets/app/agents/team/";

$gTeamdrivingDir = $gWebsiteDir. "assets/app/agents/team/Driving/";

$gTeamdrivingURL = $gWebsite."/assets/app/agents/team/Driving/";

$gTeamCVDir = $gWebsiteDir. "assets/app/agents/team/CV/";

$gTeamCVURL = $gWebsite."/assets/app/agents/team/CV/";

$gTeamOtherDir = $gWebsiteDir. "assets/app/agents/team/Other/";

$gTeamOtherURL = $gWebsite."/assets/app/agents/team/Other/";

$gLayerDir = $gWebsiteDir. "/layers/";

$gLayerURL = $gWebsite. "/layers/";


$gQuoteHashSecret = "hjsdfhj h65^ w367UYGr35trt3467^&SO^&wuhdsuhy428 wqu 83 weiow90349058 jajJJHHA*#R;";

