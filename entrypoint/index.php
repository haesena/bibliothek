<?php

$appPath = "/path/to/source";

$config = [
	'db_dsn' => "mysql:dbname=DBNAME;host=127.0.0.1",
	'db_username' => "DB_USERNAME",
	'db_password' => "DB_PASSWORD"
];

require $appPath . "src/main.php";
