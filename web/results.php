<?php
	$conn = new mysqli('localhost', 'root', '', 'nowrace');
	$conn->query("SET NAMES utf8");  
	$data = '';
	
	$result = $conn->query('SELECT * FROM `live` WHERE `race_id` = '.$_GET['id']);
	while($row = $result->fetch_assoc()) {
		$data = $row['positions'];
	}
	
	header('Content-Type: application/json');
	echo $data;
?>