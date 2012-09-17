<?php

/*
 * singleton instance
 */

require_once  'S3.php';
Class Helper {


    private static $inst;

    private function __construct(){
    }

    public static function getAmazonInstance($awsAccessKey, $awsSecretKey){

        if (self::$inst == null){
             self::$inst =  new S3($awsAccessKey, $awsSecretKey);
        }
        return self::$inst;
    }



}
?>