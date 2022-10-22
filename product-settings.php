<?php ob_start();
session_start();
include('includes/functions.php');
if(!empty($_SESSION['agentid'])){
	# for search property
/*$getprop=$db->joinquery("SELECT locationSearch FROM location WHERE agentid='".$_SESSION['agentid']."'");		
		if(mysqli_num_rows($getprop)>0){
					while($row_prop=mysqli_fetch_array($getprop)){
							     $postLocation[]=$row_prop['locationSearch'];
							}
}*/

	
	# add prodcuts
	
	if($_REQUEST['id']=='add'){
		$getsupplierlist=$db->joinquery("SELECT supplier_name,supplierid FROM suppliers");
		while($Rowsupplier=mysqli_fetch_assoc($getsupplierlist)){
			 $supplierlist[]=$Rowsupplier;
		}
		
	}
	#save products
	 if(isset($_POST['btn_add'])){
		$getPdt=$db->joinquery("SELECT * FROM products WHERE productid='".$_POST['select_produt']."'");
		$rowPdt=mysqli_fetch_assoc($getPdt);
		$db->joinquery("INSERT INTO products(`name`,`shortname`,`code`,`description`,`supplierid`,`agentid`,`linkURL`,`rrpvalue`,`hours`,`unitname`,`unittag`,`wsvalue`,`costvalue`,`imageid`)VALUES('".$rowPdt['name']."','".$rowPdt['shortname']."','".$rowPdt['code']."','".$rowPdt['description']."',0,'".$_SESSION['agentid']."','".$rowPdt['linkURL']."','".$rowPdt['rrpvalue']."','".$rowPdt['hours']."','".$rowPdt['unitname']."','".$rowPdt['unittag']."','".$rowPdt['wsvalue']."','".$rowPdt['costvalue']."','".$_POST['select_produt']."')");
	}
	
	#edit products
	
	if($_REQUEST['id']!='add'){
				$getPdt=$db->joinquery("SELECT * FROM products WHERE productid='".base64_decode($_REQUEST['id'])."'");
		  $rowPdt=mysqli_fetch_assoc($getPdt);
				if($rowPdt['imageid']==0)
				$rowPdt['imageid']=$rowPdt['productid'];
				$getunits=$db->joinquery("SELECT * FROM Units");
				while($rowUnits=mysqli_fetch_array($getunits)){
					$UnitArr[]=$rowUnits;
				}
	}
	# update prodcuts
	
	if(isset($_POST['type'])){
		$db->joinquery("UPDATE products SET name='".$_POST['product_name']."',shortname='".$_POST['short_name']."',code='".$_POST['code']."',description='".$_POST['description']."',linkURL='".$_POST['link_url']."',hours='".$_POST['hours']."',unitname='".$_POST['unit_name']."',unittag='".$_POST['unit_tag']."',wsvalue='".$_POST['ws_value']."' WHERE productid='".$_POST['productid']."'");
			if(!empty($_FILES['productimage']['name'])){
				$newproductimage = $_POST['productid'].".png" ;
				move_uploaded_file($_FILES['productimage']['tmp_name'], $gSupplierProdcutsDir.$newproductimage);
				$db->joinquery("UPDATE products SET imageid='".$_POST['productid']."' WHERE productid='".$_POST['productid']."'");
	
				}
	}
	
	
			$getproductdeatils=$db->joinquery("SELECT * FROM products WHERE agentid='".$_SESSION['agentid']."'");
			while($rowProdcuts=mysqli_fetch_assoc($getproductdeatils)){
				if($rowProdcuts['imageid']==0)
				$rowProdcuts['imageid']=$rowProdcuts['productid'];
				$Prodcuts[]=$rowProdcuts;
				
	}
	include('templates/header.php');
	if(isset($_REQUEST['id'])){
		
		 	if($_REQUEST['id']=='add'){
			include('views/add-products.htm');
	}
	else if($_REQUEST['id']!='add'){
		include('views/edit-products.htm');
	}
		
	}
	else
	{
		 		 include('views/products-settings.htm');

	}

	
include('templates/footer.php');
			
  
}
else
{
	  header('Location:index.php');
}