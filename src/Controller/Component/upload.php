<?php
/**
 * $Id: upload.php,v 1.12.14.28.2.2 2024/02/23 09:50:34 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/02/23 09:50:34 $
 */
/**
 * upload component
 */
uses('Folder');
require_once(LIBS.DS.'component_object.php');
class UploadComponent extends ComponentObject {
	var $name = 'UploadComponent';
	var $params = null;
	var $fileName = null;
	//var $uploadBase = '';
	var $components = array('Session');
	var $sessKey = '';

	/**
	 * initialization
	 */
	function initialize(&$controller) {
		$this->controller = &$controller;
		return true;
	}
	/**
	 * startup procedures
	 */
	function startup(&$controller) {
		/*$cutUrl = explode('/', $_SERVER['REQUEST_URI']);
		$urlValue = $cutUrl[1];
		$this->uploadDir = UPDIR;
		$this->uploadTmp = $this->uploadDir.DS.'tmp'.DS;
		$this->uploadImg = $this->uploadDir.DS.'imgs'.DS;
		$this->uploadBase = '/files/';*/
		/* define UPDIR, UPIMGDIR, UPTMPDIR at app_controllers beforeFilter*/
		$this->uploadDir = UPDIR;
		$this->uploadImg = UPIMGDIR;
		$this->uploadClockImg = UPCLOCKIMGDIR;
		$this->uploadTmp = UPTMPDIR;
		$this->params = $controller->params;
		$this->data = &$this->params['data'];
		if (!is_dir($this->uploadDir)) {
			mkdir($this->uploadDir);
		}
		chmod($this->uploadDir, 0777);
		if (!is_dir($this->uploadTmp)) {
			mkdir($this->uploadTmp);
		}
		chmod($this->uploadTmp, 0777);
		if (!is_dir($this->uploadImg)) {
			mkdir($this->uploadImg);
		}
		chmod($this->uploadImg, 0777);
		if (!is_dir($this->uploadClockImg)) {
			mkdir($this->uploadClockImg);
		}
		chmod($this->uploadClockImg, 0777);
	}

	/**
	 * start a new upload
	 * @param string $sessKey
	 */
	function newUpload($sessKey = null) {
		$this->Session->del($sessKey);
	}

	/**
	 * end the cycle of upload
	 * @param string $sessKey
	 */
	function endUpload($sessKey = null) {
		$this->Session->delete($sessKey);
	}

	/**
	 * add the file to temp upload
	 * @param string $sessKey
	 * @param array $fileData
	 */
	function addFile($sessKey = null,  $fileData) {
		$bname = str_replace('/', '___', base64_encode($fileData['name']));
		$newFileName = session_id() . '_' . rand(1000, 1999) . '_' . $bname;
		$newFilePath = $this->uploadTmp . $newFileName;
		move_uploaded_file($fileData['tmp_name'], $newFilePath);
		//$fileData['path'] = $this->uploadBase;
		$fileData['bname'] = $bname;
		$fileData['fspath'] = $newFilePath;
		$uploaded = array();
		if ($this->Session->check($sessKey)) {
			$uploaded = unserialize($this->Session->read($sessKey));
		}
		$uploaded[] = $fileData;
		$this->Session->write($sessKey, serialize($uploaded));
		return $fileData;
	}

	/**
	 * this takes some uploaded file and give it a new place for temporary need
	 * usually for upload file which will be processed soon, like: being previewed
	 * @param array $fileData
	 * @param bool $fullPath If set to true, return full path, otherwise just file name
	 * @param string $prefix Prefix for new file name
	 * @return string $newFilePath New temp file name/path
	 */
	function addTmpFile($fileData = null,  $fullPath = true, $prefix = 'uptmp') {
		$newFileName = uniqid($prefix);
		$newFilePath = $this->uploadTmp . $newFileName;


		if(!is_dir($this->uploadTmp)) {
			mkdir($this->uploadTmp, 0777);
		}
		move_uploaded_file($fileData['tmp_name'], $newFilePath);
		return ($fullPath === false) ? $newFileName : $newFilePath;
	}

	/**
	 * this takes some uploaded file and give it a new place for temporary need
	 * usually for upload file which will be processed soon, like: being previewed
	 * @param array $fileData
	 * @param bool $fullPath If set to true, return full path, otherwise just file name
	 * @param string $prefix Prefix for new file name
	 * @return string $newFilePath New temp file name/path
	 */
	function addFileToCloud($fileData = null,  $prefix = '', $newPath = null) {
		if(empty($prefix)){
			$newFileName = uniqid('uptmp');
		}else{
			$newFileName = $prefix;
		}
		if(!empty($newPath)){
			$newFilePath = $newPath . $newFileName;
		}else{
			$newFilePath = $this->uploadDir . $newFileName;
		}
		$checkUploadType = $this->checkUploadType();

		if($checkUploadType == 'cloud') {
			App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
			$s3 = new AmazonS3();
			$bucket = 'femascloud-files';
		} else {
			if(!is_dir($this->uploadDir)) {
				mkdir($this->uploadDir, 0777);
			}
		}

		if($checkUploadType == 'cloud') {
			$fspathCuts = explode('/', $newFilePath);
			unset($fspathCuts[0]);
			unset($fspathCuts[1]);
			$uploadDirStr = implode('/', $fspathCuts);

			$openFile = fopen($fileData['tmp_name'], 'r');
			$response = $s3->create_object($bucket, $uploadDirStr, array(
				'fileUpload' => $openFile
			));
			fclose($openFile);
			if($response->isOK()) {
				return true;
			} else {
				return false;
			}
		}else{
			if(move_uploaded_file($fileData['tmp_name'], $newFilePath)){
				return $newFileName;
			} else {
				return false;
			}
		}
	}

	/**
	 * add image file
	 * @param array $fileData
	 * @return bool true|false result of upload
	 */
	function addImage($fileData = null,  $options=null) {
		if (! isset($options['name'])) {
			$newFileName = str_replace('/', '___', base64_encode($fileData['name']));
		} else {
			$newFileName = $options['name'];
		}

		$newFilePath = $this->uploadImg . $newFileName;
		if (!empty($options['photo_width_limit'])) {
			list($photoName, $photoType) = explode('.', $newFileName);
			list($width, $height) = getimagesize($fileData['tmp_name']);
			if ($width > 400) {
				$percent = round($width / 400, 3);
				$newWidth = round($width / $percent);
				$newHeight = round($height / $percent);
				$thumb = imagecreatetruecolor($newWidth, $newHeight);
				if ($photoType == 'gif') {
					$source = imagecreatefromgif($fileData['tmp_name']);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagegif($thumb, $fileData['tmp_name']);
				} elseif ($photoType == 'png') {
					$source = imagecreatefrompng($fileData['tmp_name']);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagepng($thumb, $fileData['tmp_name']);
				} else {
					$source = imagecreatefromjpeg($fileData['tmp_name']);
					imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagejpeg($thumb, $fileData['tmp_name']);
				}
				imagedestroy($thumb);
				imagedestroy($source);
			}
		}
		$checkUploadType = $this->checkUploadType();

		if($checkUploadType == 'cloud') {
			App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
			$s3 = new AmazonS3();
			$bucket = 'femascloud-files';
		} else {
			if(!is_dir($this->uploadImg)) {
				mkdir($this->uploadImg, 0777);
			}
		}

		if($checkUploadType == 'cloud') {
			$fspathCuts = explode('/', $newFilePath);
			unset($fspathCuts[0]);
			unset($fspathCuts[1]);
			$uploadDirStr = implode('/', $fspathCuts);

			$openFile = fopen($fileData['tmp_name'], 'r');
			$response = $s3->create_object($bucket, $uploadDirStr, array(
				'fileUpload' => $openFile
			));
			fclose($openFile);
			if($response->isOK()) {
				return true;
			} else {
				return false;
			}
		} else {
			if (move_uploaded_file($fileData['tmp_name'], $newFilePath)) {
				return $newFileName;
			} else {
				return false;
			}
		}
	}

	function checkUploadType() {
		$swlic = Configure::read('EmmaLic');

		$isAmazonServer = false;
		$getOsInfo = php_uname();
		$isAmazonServer = strrpos($getOsInfo, 'amz');

		if($isAmazonServer && $swlic['prdmode'] == 'cloud') {
			return 'cloud';
		} else {
			return 'normal';
		}
	}

	function checkFileIsReadable($fileNamePath = null) {
		if($this->checkFileExists($fileNamePath)) {
			$checkUploadType = $this->checkUploadType();

			if($checkUploadType == 'cloud') {
				App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
				$s3 = new AmazonS3();
				$bucket = 'femascloud-files';
			}

			if($checkUploadType == 'cloud') {
				$fspathCuts = explode('/', $fileNamePath);
				unset($fspathCuts[0]);
				unset($fspathCuts[1]);
				$downloadDirStr = rawurldecode(implode('/', $fspathCuts));
				$response = $s3->get_object($bucket, $downloadDirStr);
				return $response->isOK();
			} else {
				return is_readable($fileNamePath);
			}
		}
	}

	function checkFileExists($fileNamePath = null,  $otherParam = array()) {
		$checkUploadType = $this->checkUploadType();
		$defaultOtherParam = array('bucket'=>'femascloud-files', 'slice'=>2);
		if(empty($otherParam)) {
			$otherParam = $defaultOtherParam;
		}
		extract($otherParam);

		if($checkUploadType == 'cloud') {
			App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
			$s3 = new AmazonS3();
		}

		if($checkUploadType == 'cloud') {
			$fileNamePath = rawurldecode(implode('/', array_slice(explode('/', $fileNamePath), $slice)));
			return $s3->if_object_exists($bucket, $fileNamePath);
		} else {
			return file_exists($fileNamePath);
		}
	}

	function addFileByDoc($fileNamePath = null,  $otherParam = array()) {
		$checkUploadType = $this->checkUploadType();
		$defaultOtherParam = array('bucket'=>'femascloud-files', 'slice'=>2);
		if(empty($otherParam)) {
			$otherParam = $defaultOtherParam;
		}
		extract($otherParam);

		if($checkUploadType == 'cloud') {
			App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
			$s3 = new AmazonS3();
		}

		if($checkUploadType == 'cloud') {
			$s3FileNamePath = implode('/', array_slice(explode('/', $fileNamePath), $slice));
			$s3FileExist = $s3->if_object_exists($bucket, $s3FileNamePath);
			if($s3FileExist){
				$fileWrite = fopen($fileNamePath, "w+");
				$response = $s3->get_object($bucket, $s3FileNamePath, array('fileDownload'=>$fileWrite));
				fclose($fileWrite);
			}
			if(file_exists($fileNamePath)) {
				return $fileNamePath;
			} else {
				return false;
			}
		} else {
			if(file_exists($fileNamePath)) {
				return $fileNamePath;
			} else {
				return false;
			}
		}
	}

	function delTmpFileByDoc($fileNamePath = null) {
		$checkUploadType = $this->checkUploadType();

		if($checkUploadType == 'cloud') {
			if(file_exists($fileNamePath)) {
				unlink($fileNamePath);
			}
		}
	}

	function fileReName($newFileNamePath = null,  $oldFileNamePath, $otherParam = array()) {
		$checkUploadType = $this->checkUploadType();
		$defaultOtherParam = array('bucket'=>'femascloud-files', 'slice'=>2);
		if(empty($otherParam)) {
			$otherParam = $defaultOtherParam;
		}
		extract($otherParam);

		if($checkUploadType == 'cloud') {
			App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
			$s3 = new AmazonS3();
		}

		if($checkUploadType == 'cloud') {
			$s3OldFileNamePath = implode('/', array_slice(explode('/', $oldFileNamePath), $slice));
			$s3NewFileNamePath = implode('/', array_slice(explode('/', $newFileNamePath), $slice));

			if($this->checkFileExists($newFileNamePath)) {
				$s3->delete_object($bucket, $s3NewFileNamePath);
			}

			if($this->checkFileExists($oldFileNamePath)) {
				$response = $s3->copy_object(array('bucket' => $bucket, 'filename' => $s3OldFileNamePath), array('bucket' => $bucket, 'filename' => $s3NewFileNamePath));
				if($response->isOK()) {
					if($this->checkFileExists($newFileNamePath) && $this->checkFileExists($oldFileNamePath)) {
						$s3->delete_object($bucket, $s3OldFileNamePath);
					}
				}
			}
		} else {
			if(file_exists ($newFileNamePath)) {
				unlink ($newFileNamePath);
			}
			rename ($oldFileNamePath, $newFileNamePath);
		}

		$newFileNamePathInfo = pathinfo($newFileNamePath);
		return $newFileNamePathInfo['basename'];
	}

	/**
	 * open the file for download
	 * @param string $fileSavedPath
	 */
	function openFile($fileSavedPath=null, $isS3Url=false, $otherParam = array()) {
		$defaultOtherParam = array('bucket'=>'femascloud-files', 'slice'=>2);
		if(empty($otherParam)) {
			$otherParam = $defaultOtherParam;
		}
		extract($otherParam);

		$checkUploadType = $this->checkUploadType();

		if($checkUploadType == 'cloud') {
			App::import('Vendor', 'Aws', array('file' =>'Aws/sdk.class.php'));
			$s3 = new AmazonS3();
		}

		if($isS3Url && $checkUploadType == 'cloud') {
			$response = $s3->get_object($bucket, rawurldecode($fileSavedPath));
			echo $response->body;
			exit();
		} else {
			if($checkUploadType == 'cloud' && !$skipS3Check) {
				$downloadDirStr = rawurldecode(implode('/', array_slice(explode('/', $fileSavedPath), $slice)));
				if($s3->if_object_exists($bucket, $downloadDirStr)) {
					$response = $s3->get_object($bucket, $downloadDirStr);
					echo $response->body;
				} else {
					readfile($fileSavedPath);
					return;
					$fp = fopen($fileSavedPath, 'rb');
					fpassthru($fp);
				}
			} else {
				readfile($fileSavedPath);
				return;
				$fp = fopen($fileSavedPath, 'rb');
				fpassthru($fp);
			}
		}
	}

	/**
	 * transfer tmp file to formal uploaded file
	 * @param string $tmpPath
	 * @param string $formalPath
	 */
	function tmpToFormal($tmpPath = null,  $formalPath) {
		rename($tmpPath, $formalPath);
	}

	/**
	 * list temp uploads, those already saved to files/tmp
	 * @param string $sessKey
	 * @return array
	 */
	function listTmpUploads($sessKey = null) {
		/**
		 * here we del Config in session to make sure it will not log out agent
		 *  though we turn off Session.agentCheck, but here we still need this
		 *  to make sure listing uploads can work fine.
		 */
		//$this->Session->del('Config');
		if ($this->Session->check($sessKey)) {
			$upInfo = unserialize($this->Session->read($sessKey));
			foreach($upInfo as $k => $v) {
				if(!file_exists($v['fspath'])) {
					unset($upInfo[$k]);
				}
			}
			return $upInfo;
		} else {
			return array();
		}
	}

	/**
	 * remove temp file and session data
	 * @param string $sessKey
	 * @param string $id the ID of file in session, not in db
	 * @return mixed false|array
	 */
	function removeFile($sessKey = null,  $id) {
		if ($this->Session->check($sessKey)) {
			$uploaded = unserialize($this->Session->read($sessKey));
			@unlink($uploaded[$id]['fspath']);
			unset($uploaded[$id]);
			$this->Session->write($sessKey, serialize($uploaded));
			return $uploaded;
		} else {
			return false;
		}
	}

	/**
	 * check if file type is allowed
	 * @param string $fileName
	 * @param mixed $allowedTypes Allowed file types, can be array or string('*.*')
	 * @return mixed false or type name or null (if given file has no suffix but all type allowed)
	 */
	function checkFileType($fileName = null,  $allowedTypes = '*.*') {
		if ($allowedTypes == '*.*') {
			return true;
		}
		$dotpos = strrpos($fileName, '.');
		if ($dotpos === false) {
			// no suffix
			$myType = null;
		} else {
			$myType = strtolower(substr($fileName, $dotpos+1));	// lowercase for comparison
		}
		if (is_array($allowedTypes)) {
			foreach ($allowedTypes as $type) {
				$type = strtolower($type);
				if ($type == '*.*') {
					return $myType;
				} elseif ($myType == $type) {
					return $myType;
				}
			}
		}
		return false;
	}

	/**
	 * export excel file, given partial file name to distinguish
	 * will set 'savePath' in view (excel save path)
	 * @param string $pfn Partial file name
	 * @param string $path If given, will find this file to download
	 */
	public function exportExcel($pfn = 'some-excel', $path = null)
	{
		if (empty($path)) {
			$fpath = UPTMPDIR. session_id() . '-' . $pfn . '.xls';
		} else {
			$fpath = $path;
		}
		$this->controller->set('savePath', $fpath);
		$this->controller->render($this->controller->action);

		if (is_readable($fpath)) {
			header("content-disposition: attachment");
			$this->openFile($fpath);
			unlink($fpath);
		}
	}

	/**
	 * do the upload process, set 'mesg' & ('upfile' or 'alert') in view
	 * if success return true
	 * @param string $fileData Usually $controller->data['Model']['Filedata']
	 * @return bool
	 */
	public function procUpload($fileData = null)
	{
		# code...
		$success = true;
		if (!empty($fileData['name'])) {
			$upfile = $this->addTmpFile($fileData);
			$this->controller->set('upfile', $upfile);
			$this->controller->set('mesg', '上傳成功, 請稍候...');
			$this->controller->set('success', 1);
			$this->controller->set('uploadSavedPath', base64_encode($upfile));
			$this->controller->set('uploadSentFile', base64_encode($fileData['name']));
		} else {
			// alert
			$this->controller->set('alert', '檔案上傳失敗! 請重新選擇檔案上傳');
			$this->controller->set('mesg', '上傳失敗');
			$success = false;
		}
		return $success;
	}

	/**
	 * extract data, model should attach behavior 'FileImporter' first
	 * @param object $model
	 * @param array $fieldDescs
	 * @param string $sent
	 * @param string $saved
	 * @param array $output Content need to output
	 */
	public function extractDataFile(&$model, $fieldDescs, $sent = null, $saved = null, $output = array('lines' => true, 'data' => true))
	{
		// this should run in action after previous action run the procUpload()
		if (empty($sent)) {
			$sent = base64_decode($this->controller->params['form']['sent']);
		}
		if (empty($saved)) {
			$saved = base64_decode($this->controller->params['form']['saved']);
		}

		$ftype = $model->getFileType($sent);
		$lines = array();
		$data = array();
		// split field
		if (isset($output['lines'])) {
			$lines = $model->parseFileToLines($saved, $sent, array('split' => true));
		}
		if (isset($output['data'])) {
			$data = $model->readImportData($saved, $fieldDescs, $ftype);
		}

		return array($lines, $data);
	}

	public function download($fpath = null,  $unlink = true)
	{
		// we can modify below to support more format and encodings
		if (is_readable($fpath)) {
			header('Content-Type: application/octet-stream');
			header("Content-Disposition: attachment");
			header("Content-Transfer-Encoding: binary");
			$this->openFile($fpath);
			if ($unlink) {
				unlink($fpath);
			}
		}
	}
}
?>
