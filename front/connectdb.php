<?php
		$host = "localhost";
		$user = "root";
		$pwd = "66010914015";
		$db = "2m3wm";
		$conn = mysqli_connect($host, $user, $pwd, $db) or die ("เชื่อมต่อฐานข้อมูลไม่ได้");
		mysqli_query($conn, "SET NAMES utf8");
?>