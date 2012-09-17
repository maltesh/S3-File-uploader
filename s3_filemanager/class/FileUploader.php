<?php

/*
 * GPL License
 */


class FileUploader {

    public    $file;
    public    $allowed_file_types = array();
    private   $upload_path ='/tmp/';
    private   $file_name;

    function __construct($files) {
        $this->file = $files;
    }

    public function __validate(){

        return (count($this->file)>0)?true:false;
    }


    public function isFileUploaded(){
        $is_uploaded = is_uploaded_file($this->file['tmp_name']);
        if(!$is_uploaded){
            $error_number = $this->file['error'];
            if($error_number === 0){
                return true;
            }else{
                switch($error_number){
                    case 1:	// UPLOAD_ERR_INI_SIZE
                        $this->flag_error('upload_file_exceeds_limit');
                        break;
                    case 2: // UPLOAD_ERR_FORM_SIZE
                        $this->flag_error('upload_file_exceeds_form_limit');
                        break;
                    case 3: // UPLOAD_ERR_PARTIAL
                    $this->flag_error('upload_file_partial');
                        break;
                    case 4: // UPLOAD_ERR_NO_FILE
                    $this->flag_error('upload_no_file_selected');
                        break;
                    case 6: // UPLOAD_ERR_NO_TMP_DIR
                        $this->flag_error('upload_no_temp_directory');
                        break;
                    case 7: // UPLOAD_ERR_CANT_WRITE
                        $this->flag_error('upload_unable_to_write_file');
                        break;
                    case 8: // UPLOAD_ERR_EXTENSION
                        $this->flag_error('upload_stopped_by_extension');
                        break;
                    default :   $this->flag_error('upload_no_file_selected');
                        break;
                }
            }
        }else{
            return true;
        }
    }

    protected function setFileParams(){

        $this->file_temp = $this->file['tmp_name'];
		$this->file_size = $this->getFileSize($this->file['size']);
		$this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES[$field]['type']);
		$this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
		$file_name       = $this->clean_file_name($this->file['name']);
		$this->file_ext	 = $this->get_extension($file_name);

        $this->setFileName($file_name);
        $this->validateFileSize();

    }

    private function setFileName($file_name){
        $this->file_name = $file_name;
    }

    public function getFileName(){
        return $this->file_name;
    }

    public function getUploadPath(){
        return $this->upload_path;
    }


    private function validateFileSize(){

    }

    /*
     * Pass $_FILES['size] value as i/p
    */

    public function getFileSize($file_size){
        if ($file_size > 0){
			return round($file_size/1024, 2);
		}
    }


    public function get_extension($file_name){
        $x = explode('.', $file_name);
		return $x[1];
    }

    /*
     *
     */
    private function clean_file_name($filename){
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						"%20",
						"%22",
						"%3c",		// <
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (
						"%29", 		// )
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);

		$filename = str_replace($bad, '', $filename);
		return stripslashes($filename);
	}

    /*
     * Try copy function and if it fails try move_uploaded_file
     */

    protected function copyFile(){
        if ( ! @copy($this->file_temp, $this->getUploadPath().$this->getFileName())){
			if ( ! @move_uploaded_file($this->file_temp, $this->getUploadPath().$this->getFileName())){
				 $this->flag_error('Bad Destination');
			}
		}

    }


    private function flag_error($message){
        return $message;
    }


    function __destruct(){




    }


}
?>
