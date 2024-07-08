<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<style>
	body, input{
		font-size:2rem;
	}
</style>
</head>
<body>
<?php
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); //允許跨來源 此三種方法ok
header("Access-Control-Allow-Headers: Content-Type"); //允許跨來源
header("Content-Type: application/json"); //回應為json檔


include '../connectCid101g6.php'; 
//--------------------從資料庫取得此票券訂單的資料
//"成人票":100, "兒童票":60, "敬老票":20
$orders = [
	["order_no"=>8, "order_date"=>"2024-01-01", "total"=>520, 
		"ticket_type"=>["成人票"=>4, "兒童票"=>2], "used" =>true],
	["order_no"=>12, "order_date"=>"2024-01-30", "total"=>4822220, 
		"ticket_type"=>["成人票"=>2, "兒童票"=>4, "敬老票"=> 2], "used" =>false],
	["order_no"=>16, "order_date"=>"2024-03-08", "total"=>240, 
		"ticket_type"=>["成人票"=>2, "敬老票"=> 2], "used" =>false]
];

$index = false;
$order_no = $_GET["order_no"];
foreach($orders as $i=>$order){
	if($order["order_no"] == $order_no) {
		$index = $i;
		break;
	}
}
//--------------------從資料庫取得此票券訂單的資料(end)

if($index === false) {
	echo "查無此訂單編號";
} else {
	if(! $orders[$index]["used"]) {
		echo "票券訂單編號: {$orders[$index]["order_no"]}<br>";
		echo "訂購日期: {$orders[$index]["order_date"]}<br>";
		echo "票券總價: {$orders[$index]["total"]}<hr><br>";
		echo "訂單細項: <br>";
		foreach($orders[$index]["ticket_type"] as $ticket_type => $quantity) {
			echo "$ticket_type: $quantity<br> ";
		}
		echo "<input type='button' value='確定入場' onclick='checkIn()'><br><br>";
	}
}
?>	
<script>
function checkIn() {
	if(confirm("確定入場")) {
		//update db
		alert("票券已使用");
	}
}	
</script>
</body>
</html>