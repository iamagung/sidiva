<?php 
namespace App\Http\Libraries;

class compressFile
{	
	public static function UploadCompress($new_name,$file,$dir,$quality)
	{
		//direktori gambar
        $vdir_upload = $dir;
        $vfile_upload = $vdir_upload . $_FILES[''.$file.'']["name"];
        //Simpan gambar dalam ukuran sebenarnya
        move_uploaded_file($_FILES[''.$file.'']["tmp_name"], $dir.$_FILES[''.$file.'']["name"]);
        $source_url=$dir.$_FILES[''.$file.'']["name"];
        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg'){ 
            $image = imagecreatefromjpeg($source_url); 
            $ext='.jpg';
        }elseif($info['mime'] == 'image/gif'){ 
            $image = imagecreatefromgif($source_url); 
            $ext='.gif';
        }elseif($info['mime'] == 'image/png'){ 
            $image = imagecreatefrompng($source_url); 
            $ext='.png';
        }
        if(imagejpeg($image, $dir.$new_name.$ext, $quality)){
            unlink($source_url);
            return true;
        }else{
            unlink($source_url);
            return false;
        }
	}
}
