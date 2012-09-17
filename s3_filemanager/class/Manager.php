<?php

/*
 * GPL
 * @author-zizou5mal@gmail.com
 */


require_once 'Config.php';
require_once 'FileUploader.php';
require_once 'Helper.php';


error_reporting(E_ALL && ~E_NOTICE);
ini_set('display_errors','On');


class Manager extends FileUploader implements AmazonConstants{

    //This is the input element
    private $file_key='Filedata';

    function __construct ($files){
        parent::__construct($files[$this->file_key]);
    }

    public function __validate(){
        $validate = parent::__validate();
        if($validate){
            $file_uploaded = parent::isFileUploaded();
            if($file_uploaded){
                $this->setFileParams();
                return true;
            }else{
                return false;
            }
        }
    }

    public function copyFile(){
        parent::copyFile();
        $this->copyToAmazon();
    }

    public function copyToAmazon(){
        $this->amazon_instance = Helper::getAmazonInstance(self::S3_ACCESSKEY,self::S3_SECRETKEY);
        $check_file_exists =  $this->amazon_instance->getObjectInfo(self::BUCKETNAME, basename($this->getFileUrl()));

        if($check_file_exists == false){
            $this->amazon_instance->putObjectFile($this->getUploadPath() . $this->getFileName(),  self::BUCKETNAME,basename($this->getFileName()),S3::ACL_PUBLIC_READ);
            echo $this->getFileUrl();
        }else{
            $unique_file_name = $this->getUniqueFileName();
            $this->amazon_instance->putObjectFile($this->getUploadPath() . $this->getFileName(),  self::BUCKETNAME,basename($unique_file_name),S3::ACL_PUBLIC_READ);
            echo $unique_file_name;
        }

    }

    protected function __clone(){

    }

    private function getFileUrl(){
        return 'https://'.self::BUCKETNAME.'.s3.amazonaws.com/'.$this->getFileName();
    }

    private function getUniqueFileName(){
        return 'https://'.self::BUCKETNAME.'.s3.amazonaws.com/'.'v_'.rand(1,10000).$this->getRandomString().'_'.$this->getFileName();
    }

    private function getRandomString(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++){
                $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }

    public function __destruct(){

    }



}

?>
