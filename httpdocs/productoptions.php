<?php
# includes
include("files/constants.php");
include("files/library/session.php");
include("files/library/formlib.php");
include("files/library/common.php");
if (isset($_REQUEST['cancel'])) {
	header("Location: " . $_SERVER['PHP_SELF'] . "?type=" . $_REQUEST["type"]);
	exit(0);
}
# connect to database
$mysqli = new mysqli($gDBHost, $gDBUser, $gDBPassword, $gDBName);
if ($mysqli->connect_errno)
	throw new Exception($mysqli->connect_error, $mysqli->connect_errno);
$mysqli->set_charset('UTF');
$adminId = SessionCheck($mysqli);
if (isset($_REQUEST["id"])) {
	$form = new Form($_POST);
	if ($_REQUEST['id'] != 'add') {
		$id = intval($_REQUEST["id"]);
		if ($_REQUEST['type'] == 'products')
			$querySQL = "SELECT * FROM products WHERE productid = $id";
		else if ($_REQUEST['type'] == 'suppliers')
			$querySQL = "SELECT * FROM suppliers WHERE supplierid = $id";
		if (!($query = $mysqli->query($querySQL)))
			throw new Exception($mysqli->error);
		$option = $query->fetch_assoc();
		$query->free();
		if ($_REQUEST['type'] == 'products') {
			if ($option['productid'] != $_REQUEST["id"])
				throw new Exception("Invalid argument");
		} else if ($_REQUEST['type'] == 'suppliers') {
			if ($option['supplierid'] != $_REQUEST["id"])
				throw new Exception("Invalid argument");
		}
	} else {
		$id = "add";
		$option = array('name' => '', 'value' => '', 'unittag' => 'cm2');
	}
	if (isset($_REQUEST['save'])) {
		$fields = array();
		$errors = array();
		if ($_REQUEST['type'] == "products") {
			if (trim($_REQUEST['name']) == '')
				$errors['name'] = 'Required';
			else
				array_push($fields, "name = '" . $mysqli->real_escape_string($_REQUEST['name']) . "'");
			if (trim($_REQUEST['shortname']) == '')
				$errors['shortname'] = 'Required';
			else
				array_push($fields, "shortname = '" . $mysqli->real_escape_string($_REQUEST['shortname']) . "'");
			if (trim($_REQUEST['code']) == '')
				$errors['code'] = 'Required';
			else
				array_push($fields, "code = '" . $mysqli->real_escape_string($_REQUEST['code']) . "'");
			array_push($fields, "supplierid = " . intval($_REQUEST['supplierid']));
			if (!is_numeric($_REQUEST['rrpvalue']))
				$errors['rrpvalue'] = 'Invalid / Required';
			else
				array_push($fields, "rrpvalue = " . floatval($_REQUEST['rrpvalue']));
			if (!is_numeric($_REQUEST['wsvalue']))
				$errors['wsvalue'] = 'Invalid / Required';
			else
				array_push($fields, "wsvalue = " . floatval($_REQUEST['wsvalue']));
			if (!is_numeric($_REQUEST['costvalue']))
				$errors['costvalue'] = 'Invalid / Required';
			else
				array_push($fields, "costvalue = " . floatval($_REQUEST['costvalue']));
			if (!is_numeric($_REQUEST['hours']))
				$errors['hours'] = 'Invalid / Required';
			else
				array_push($fields, "hours = " . floatval($_REQUEST['hours']));
			array_push($fields, "linkURL = '" . $mysqli->real_escape_string($_REQUEST['linkURL']) . "'");
			array_push($fields, "description = '" . $mysqli->real_escape_string($_REQUEST['description']) . "'");
			array_push($fields, "unitname = '" . $mysqli->real_escape_string($_REQUEST['unitname']) . "'");
			array_push($fields, "unittag = '" . $mysqli->real_escape_string($_REQUEST['unittag']) . "'");
		} else if ($_REQUEST['type'] == "suppliers") {
			if (trim($_REQUEST['supplier_name']) == '')
				$errors['supplier_name'] = 'Required';
			else
				array_push($fields, "supplier_name = '" . $mysqli->real_escape_string($_REQUEST['supplier_name']) . "'");
			if (trim($_REQUEST['brand']) == '')
				$errors['brand'] = 'Required';
			else
				array_push($fields, "brand = '" . $mysqli->real_escape_string($_REQUEST['brand']) . "'");
			if (trim($_REQUEST['address']) == '')
				$errors['address'] = 'Required';
			else
				array_push($fields, "address = '" . $mysqli->real_escape_string($_REQUEST['address']) . "'");
			if (trim($_REQUEST['city']) == '')
				$errors['city'] = 'Required';
			else
				array_push($fields, "city = '" . $mysqli->real_escape_string($_REQUEST['city']) . "'");
			if (trim($_REQUEST['country']) == '')
				$errors['country'] = 'Required';
			else
				array_push($fields, "country = '" . $mysqli->real_escape_string($_REQUEST['country']) . "'");
			if (trim($_REQUEST['phone']) == '')
				$errors['phone'] = 'Required';
			else
				array_push($fields, "phone = '" . $mysqli->real_escape_string($_REQUEST['phone']) . "'");
			if (trim($_REQUEST['email']) == '')
				$errors['email'] = 'Required';
			else
				array_push($fields, "email = '" . $mysqli->real_escape_string($_REQUEST['email']) . "'");
			array_push($fields, "website = '" . $mysqli->real_escape_string($_REQUEST['website']) . "'");
			array_push($fields, "contactperson = '" . $mysqli->real_escape_string($_REQUEST['contactperson']) . "'");
			if (!is_numeric($_REQUEST['industryid']))
				$errors['industryid'] = 'Invalid / Required';
			else
				array_push($fields, "industryid = " . floatval($_REQUEST['industryid']));
		}
		if (count($errors) == 0) { # save it
			// print_r(count($errors)); die();
			if ($_REQUEST['id'] == 'add') { # create new option
				if ($_REQUEST['type'] == 'products')
					$querySQL = "INSERT INTO products () VALUES ()";
				else
					$querySQL = "INSERT INTO suppliers () VALUES ()";
				if (!($query = $mysqli->query($querySQL)))
					throw new Exception($mysqli->error);
				$id = $mysqli->insert_id;
			}
			# update option
			if ($_REQUEST['type'] == 'products')
				$querySQL = "UPDATE products SET " . implode(",", $fields) . " WHERE productid = $id";
			else
				$querySQL = "UPDATE suppliers SET " . implode(",", $fields) . " WHERE supplierid = $id";
			if (!($query = $mysqli->query($querySQL)))
				throw new Exception($mysqli->error);
			# process image (if available)
			if ($_FILES['image']['tmp_name'] != '') {
				if (!move_uploaded_file($_FILES['image']['tmp_name'], $gWindowOptionProductsDir . $id . ".png"))
					throw new Exception("Unable to create " . $gWindowOptionProductsDir . $id . ".png");
			}
			# done
			header("Location: " . $_SERVER['PHP_SELF'] . "?type=" . $_REQUEST['type'] . "");
			$mysqli->close();
			exit(0);
		} else
			$message = "<div class=\"alert alert-danger\" role=\"alert\">Please correct the indicated errors</div>";
	}
	$getUnits = "SELECT * FROM Units ORDER BY unitName ASC";
	if (!($query = $mysqli->query($getUnits)))
		throw new Exception($mysqli->error);
	$units = array();
	while ($row_units = $query->fetch_assoc()) {
		$units[] = $row_units;
	}
	$getIndstry = "SELECT industryid,name FROM industry ORDER BY name ASC";
	if (!($query = $mysqli->query($getIndstry)))
		throw new Exception($mysqli->error);
	$industry = array();
	while ($row_industry = $query->fetch_assoc()) {
		$industry[] = $row_industry;
	}
	$getSuppliers = "SELECT supplier_name,supplierid FROM suppliers ORDER BY supplier_name ASC";
	if (!($query_supple = $mysqli->query($getSuppliers)))
		throw new Exception($mysqli->error);
	$suppliers = array();
	while ($row_supple = $query_supple->fetch_assoc()) {
		$suppliers[] = $row_supple;
	}
	if ($_REQUEST['type'] == 'products')
		$pageContent = "files/templates/productsedit.htm";
	else
		$pageContent = "files/templates/suppliersedit.htm";
} else {
	if ($_REQUEST['type'] == 'products')
		$querySQL = "SELECT * FROM products WHERE supplierid!='' ORDER BY name";
	else
		$querySQL = "SELECT * FROM suppliers ORDER BY supplier_name";
	if (!($query = $mysqli->query($querySQL)))
		throw new Exception($mysqli->error);
	$options = array();
	while ($option = $query->fetch_assoc()) {
		if ($_REQUEST['type'] == 'products') {
			$querySupp = "SELECT supplier_name FROM suppliers WHERE supplierid = " . $option['supplierid'] . "";
			if (!($query_supp = $mysqli->query($querySupp)))
				throw new Exception($mysqli->error);
			$supp = $query_supp->fetch_assoc();
			$option['supplier_name'] = $supp['supplier_name'];
		} else {
			$getindustryname = "SELECT name FROM industry WHERE industryid='" . $option['industryid'] . "'";
			if (!($query_inds = $mysqli->query($getindustryname)))
				throw new Exception($mysqli->error);
			$indus_name = $query_inds->fetch_assoc();
			$option['industryid'] = $indus_name['name'];
		}
		array_push($options, $option);
	}
	$query->free();
	if ($_REQUEST['type'] == 'products') {
		$pageContent = "files/templates/productList.htm";
	} else {
		$pageContent = "files/templates/suppliersList.htm";
	}
}
$mysqli->close();
include("files/templates/templateadmin.htm");
exit(0);
