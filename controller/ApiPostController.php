<?php

class ApiPostController extends Controller
{
    public function __construct()
    {
        parent::__construct('');
    }

    public function changeLocale($locale)
    {
        setcookie('locale', $locale);
    }

    public function removeFile($name){
        if (file_exists("files/".$name)) {
            unlink("files/$name");
            return "remove done";
        }else
            return "file no exist";
    }

    public function uploadFile()
    {
        $dir = '/home/moxa/web/files/';
        // 檢查檔案是否上傳成功
        if (UPLOAD_ERR_OK === $_FILES['my_file']['error']) {
            echo '檔案名稱: '.$_FILES['my_file']['name'].'<br/>';
            echo '檔案類型: '.$_FILES['my_file']['type'].'<br/>';
            echo '檔案大小: '.($_FILES['my_file']['size'] / 1024).' KB<br/>';
            // echo '暫存名稱: '.$_FILES['my_file']['tmp_name'].'<br/>';

            // 檢查檔案是否已經存在
            if (file_exists($dir.$_FILES['my_file']['name'])) {
                echo '檔案已存在。<br/>';
            } else {
                $file = $_FILES['my_file']['tmp_name'];
                $dest = $dir.$_FILES['my_file']['name'];

                // 將檔案移至指定位置
                move_uploaded_file($file, $dest);
            }
        } else {
            echo '錯誤代碼：'.$_FILES['my_file']['error'].'<br/>';
            $code = $_FILES['my_file']['error'];
            switch ($code) {
                case UPLOAD_ERR_INI_SIZE:
                    $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = "The uploaded file was only partially uploaded";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = "Missing a temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = "File upload stopped by extension";
                    break;
    
                default:
                    $message = "Unknown upload error";
                    break;
            }
            echo $message;
        }

        //    header('Location: /system');
    }
}
