<?php
include('database.php');
$db = new Database();

	
/*Insert Data Starts*/

if(($_POST['action'] == 'save-pipeline') || ($_POST['action'] == 'save-production'))
{
	 if(empty($_POST['value']))
		{
			 $value="";
		}
		else
		{
			 $value=$_POST['value'];
		}
		if($_POST['action'] == 'save-production')
		{
			 $res_customer=$db->sel_rec("sales_data", "Customer","id = '".$_POST['customer']."'");
				$row_cust=mysql_fetch_array($res_customer);
				$customer=$row_cust['Customer'];

		}
		else
		{
			 $customer=$_POST['customer'];
		}
	 $pipe_data=array('type'=>$_POST['type'],'Address'=>$_POST['address'],'Customer'=>$customer,'AvePrice'=>$_POST['avgprice'],'TotalPrice'=>$_POST['totalprice'],'Product'=>$_POST['product'],'Date'=>date('Y-m-d',strtotime($_POST['datepicker'])),'value'=>$value);
		$save_data_pipe = $db->ins_rec("sales_data", $pipe_data);	
}

if($_POST['action'] == 'insert_pipeline')
{
	$graph_data = array(
			'value'	 => $_POST['pipeline'],
			'type'	 => 'pipeline',
			
		);
		
	$graph = $db->ins_rec("graph_data", $graph_data);	
}

   
	
	

if($_POST['action'] == 'insert_production')
{
	 $pipe_count = $db->sel_rec("graph_data", "*","type = 'pipeline'");
	
	$prod_count = $db->sel_rec("graph_data", "*","type = 'production'");
	
	
	$pipe_count_val = 0;
	
	if(mysql_num_rows($pipe_count)>0)
	{
		while($pipe_count_res  = mysql_fetch_array( $pipe_count))
		{
			$pipe_count_val += $pipe_count_res['value'];
		}
	}
	else
	{
		$pipe_count_val = 0;
	}
	
	$prod_count_val = 0;
	
	if(mysql_num_rows($prod_count)>0)
	{
		while($prod_count_res  = mysql_fetch_array( $prod_count))
		{
			$prod_count_val += $prod_count_res['value'];
		}
	}
	else
	{
		$prod_count_val = 0;
	}
	
	
	$pipe_pending = $pipe_count_val - $prod_count_val;
	
	if($pipe_pending != 0 && $pipe_pending >= $_POST['production']){
		
	$graph_data = array(
			'value'	 => $_POST['production'],
			'type'	 => 'production',
			
		);
		
	$graph = $db->ins_rec("graph_data", $graph_data);	
	}
	else
	{
		echo "Pending pipe count is ".$pipe_pending;
	}
}

/*Insert Data Ends*/

/*GRAPH DATA STARTS*/

$year = date('Y');
$month = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

$pipeline = 0;

$production = 0;

foreach($month as $month_res)
{
	$graph_data_pipeline = $db->sel_rec("graph_data", "*","DATE_FORMAT(date_val,'%Y-%m') = '".$year."-".$month_res."' and type = 'pipeline'");
	
	if(mysql_num_rows($graph_data_pipeline)>0)
	{
		while($graph_pipe = mysql_fetch_array( $graph_data_pipeline))
		{
			
			$pipeline += $graph_pipe['value'];
		}
	}
	else
	{
		 $pipeline = 0;
	}
	
	$pipleline_arr .= $pipeline.',';
	
	
	
	
	$graph_data_production = $db->sel_rec("graph_data", "*","DATE_FORMAT(date_val,'%Y-%m') = '".$year."-".$month_res."' and type = 'production'");
	
	if(mysql_num_rows($graph_data_production)>0)
	{
		while($graph_prod = mysql_fetch_array( $graph_data_production))
		{
			
			$production += $graph_prod['value'];
		}
	}
	else
	{
		 $production = 0;
	}
	
	$production_arr .= $production.',';
	
}

/*GRAPH DATA ENDS*/


/*PIPELINE TOTAL STARTS*/
	
	$year = date('Y');
	
	$pipe_data = $db->sel_rec("graph_data", "*","DATE_FORMAT(date_val,'%Y') = '".$year."' and type = 'pipeline'");
	
	$production_data = $db->sel_rec("graph_data", "*","DATE_FORMAT(date_val,'%Y') = '".$year."' and type = 'production'");
	
	$pipe_total = 0;
	
	if(mysql_num_rows($pipe_data)>0)
	{
		while($pipe_res = mysql_fetch_array( $pipe_data))
		{
			$pipe_total += $pipe_res['value'];
		}
		
		
	}
	else
	{
		$pipe_total = 0;
	}
	
	
	$producton_total = 0;
	
	if(mysql_num_rows($production_data)>0)
	{
		while($production_res = mysql_fetch_array( $production_data))
		{
			$producton_total += $production_res['value'];
		}
		
		
	}
	else
	{
		$producton_total = 0;
	}
	
	$pipe_total_final = $pipe_total - $producton_total;
	
	
		
/*PIPELINE TOTAL ENDS*/



/*WEEKLY VALUES STARTS*/

$sunday = strtotime("last sunday");
$sunday = date('w', $sunday)==date('w') ? $sunday+7*86400 : $sunday;
 
$saturday = strtotime(date("Y-m-d",$sunday)." +6 days");
 
$this_week_sd = date("Y-m-d",$sunday);
$this_week_ed = date("Y-m-d",$saturday);
 
$pipe_data_weekly = $db->sel_rec("graph_data", "*","(date_val BETWEEN '".$this_week_sd."' AND '".$this_week_ed."') and type = 'pipeline'");

$production_data_weekly = $db->sel_rec("graph_data", "*","(date_val BETWEEN '".$this_week_sd."' AND '".$this_week_ed."') and type = 'production'");

$pipe_total_weekly = 0;
	
if(mysql_num_rows($pipe_data_weekly)>0)
{
	while($pipe_res_weekly = mysql_fetch_array( $pipe_data_weekly))
	{
		$pipe_total_weekly += $pipe_res_weekly['value'];
	}
	
	
}
else
{
	$pipe_total_weekly = 0;
}



$production_total_weekly = 0;
	
if(mysql_num_rows($production_data_weekly)>0)
{
	while($production_res_weekly = mysql_fetch_array( $production_data_weekly))
	{
		$production_total_weekly += $production_res_weekly['value'];
	}
	
	
}
else
{
	$production_total_weekly = 0;
}

// $pipe_weekly_final = $pipe_total_weekly - $production_total_weekly;

$pipe_ratio = 100/$pipe_total_weekly;

$average_ratio = round($pipe_ratio * $production_total_weekly);

/*WEEKLY VALUES ENDS*/