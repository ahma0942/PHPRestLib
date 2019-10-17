<?php
class Names {
    function __construct(){

    }

    public static function test(Request $request, $data){
    	print_r($data);
        echo "TEST STATIC METHOD";
    }
}
