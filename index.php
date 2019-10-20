<?php
include 'PHPRestLib.php';
include 'Middleware.php';
include 'Names.php';

function test(Request $request, $data){
    echo "TEST PUBLIC FUNCTION";
}
$rest=new PHPRestLib();

$rest->get('/test',function(Request $request, $data){
	echo "TEST ANONYMOUS FUNCTION";
},['Middleware.Authorization']);

$rest->get('/test','test',['Middleware.Authorization']);

$rest->get('/test','Names.test',['Middleware.Authorization']);
