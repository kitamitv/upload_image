<?
  /**  初版	2009-05-02	*/
	/** 第2版	2009-11-10	幅の指定を可能に・PNG形式での出力の可能に */
	/** 第3版	2011-07-31	jpgファイルもアップロード可能に */
	/** 第4版	2011-08-26	黒枠の選択ができるように */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<htmllang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>画像アップロード管理画面</title>
</head>
<body>

<form name="update" method="post" action="image_upload.php5?mode=upload" enctype="multipart/form-data">
	<input type="file" name="upfile" size="50"><br>

<?
 	$mode = $_GET["mode"];

	/** 最大幅を決定 */
	if ( $_POST["longside"] == 2){
		$length = 340;
		echo "<label><input type=\"radio\" name=\"longside\" value=4 >150pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=1 >280pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=2 checked >340pixel</label><br />";
		echo "<label><input type=\"radio\" name=\"longside\" value=3 >390pixel</label><br />";
	}
	else if ( $_POST["longside"] == 1){
		$length = 280;
		echo "<label><input type=\"radio\" name=\"longside\" value=4 >150pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=1 checked >280pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=2 >340pixel</label><br />";	
		echo "<label><input type=\"radio\" name=\"longside\" value=3 >390pixel</label><br />";	
	}
	else if ( $_POST["longside"] == 4){
		$length = 150;
		echo "<label><input type=\"radio\" name=\"longside\" value=4 checked>150pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=1 >280pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=2 >340pixel</label><br />";	
		echo "<label><input type=\"radio\" name=\"longside\" value=3 >390pixel</label><br />";	
	}
	else {
		$length = 390;
		echo "<label><input type=\"radio\" name=\"longside\" value=4 >150pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=1 >280pixel</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"longside\" value=2 >340pixel</label><br />";	
		echo "<label><input type=\"radio\" name=\"longside\" value=3 checked >390pixel</label><br />";	
	}

	if ( $_POST["filetype"] == 2){
		echo "<label><input type=\"radio\" name=\"filetype\" value=1 >jpg形式</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"filetype\" value=2 checked >png形式</label><br />";
		$out_filename ="iphoneimages/iphoneimage_"  . date("Y-m-d_H-i-s"). ".png"; ;
	}
	else{
		echo "<label><input type=\"radio\" name=\"filetype\" value=1 checked >jpg形式</label>&nbsp;";
		echo "<label><input type=\"radio\" name=\"filetype\" value=2 >png形式</label><br />";
		$out_filename ="iphoneimages/iphoneimage_"  . date("Y-m-d_H-i-s"). ".jpg"; ;
	}

	echo "<label><input type=\"radio\" name=\"box\" value=1 checked >黒枠あり</label>&nbsp;";
	echo "<label><input type=\"radio\" name=\"box\" value=2 >黒枠なし</label><br />";

?>
	<input name="submit" type="submit" value="アップロードする">
</form>

<?
	$file_nm = $_FILES['upfile']["tmp_name"];

	if ( is_uploaded_file($file_nm) && $mode="upload"  ) {
			

		if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "upload/" . $_FILES["upfile"]["name"])) {

			chmod("upload/" . $_FILES["upfile"]["name"], 0644);

			$extension = end(explode('.', $_FILES["upfile"]["name"]));

			echo $_FILES["upfile"]["name"] . "をアップロードしました。";

			$url = "http://www.kitami.tv/upload/" . $_FILES["upfile"]["name"] ;
			//$url = "uploadtest/" . $_FILES["upfile"]["name"] ;

			$size = getImageSize( $url );			/** width = size[0] , height = size[1] */

			if($size[0] >= $size[1]){
				$width = $length;
				$high = round( $size[1] * $length / $size[0] );
			}else{
				$width = round( $size[0] * $length / $size[1] );
				$high = $length;
			}

			if ($extension == 'jpg' ){
				$img_in = imagecreatefromjpeg( $url );
			}else{
				$img_in = ImageCreateFromPng( $url );	/** 元画像を生成 */
			}

			$black = imagecolorallocate($img_in, 0, 0, 0);

			$img_out=ImageCreateTruecolor($width,$high);

			if($_POST["box"] == 1){
				imagerectangle( $img_in,0, 0, $size[0]-1, $size[1]-1, $black );					/** 黒枠を描く */
			}

			ImageCopyResampled( $img_out,$img_in,0,0,0,0,$width,$high,$size[0],$size[1] );	/** リサイズ   */

			
			/** 画像ファイルの書き出し */
			if ( $_POST["filetype"] == 2){
				ImagePNG($img_out, $out_filename);
			}
			else{
				ImageJPEG($img_out, $out_filename,90);
			}

			ImageDestroy($img_in);
			ImageDestroy($img_out);
		
			unlink( "upload/" . $_FILES["upfile"]["name"] );	/** 元画像を削除 */
			echo "<br><br><br><br>";

			if($length == 150){
					$out_html = "<img src=\"http://www.kitami.tv/" . $out_filename . "\" width=" .$width. " height=" . $high . " alt='iPhone' class='alignleft' align='left' border='0'>";
			}
			else{
					$out_html = "<img src=\"http://www.kitami.tv/" . $out_filename . "\" width=" .$width. " height=" . $high . " alt='iPhone' >";
			}

			echo "<textarea name=q_body  rows=4 cols=70>" . $out_html . "</textarea><br>";
			echo "<br><br>";
			echo $out_html;

		
		} else {
			echo "ファイルをアップロードできません。";
		}
	} else {
		echo "ファイルが選択されていません。";
	}
?>

</body>
</html>


