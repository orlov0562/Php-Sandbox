<?php
include 'idiorm.php';
ORM::configure('mysql:host=localhost;dbname=mysql');
ORM::configure('username', 'mysql');
ORM::configure('password', 'sandbox');
ORM::configure('driver_options', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);