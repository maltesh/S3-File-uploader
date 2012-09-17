<?php

/*
 * S3 FileManager-Entry point to S3 uploads
 *
 */


require_once 'class/Manager.php';

$manager = new Manager($_FILES);

$validate = $manager->__validate();
if($validate !=true){
    //Basically message
    echo $validate;
    exit;
}
// Copy localy and then upload the file to s3.
//
$manager->copyFile();


?>