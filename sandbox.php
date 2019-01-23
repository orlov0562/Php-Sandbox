<?php
include 'idiorm.php';
ORM::configure('mysql:host=localhost;dbname=sandbox');
ORM::configure('username', 'mysql');
ORM::configure('password', 'mysql');
ORM::configure('driver_options', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);

$item = ORM::for_table('sandbox')->order_by_desc('id')->limit(1)->find_one();

$lastId = $item->id ?? 0;

for($i=0; $i<10; $i++) {
    $title = 'Record #'.($lastId + $i + 1);
    $item = ORM::for_table('sandbox')->create();
    $item->title = $title;
    $item->created_at = time();
    $item->save();
}