<?php

namespace App\Extra;

use File;
use Storage;

class FileLibrary
{
	private $uploadPath = "public";
	private $uploadStore = "local";
	
	public function __construct()
	{
		
	}
	
	public function multiUpload(array $files, String $path = "", String $store = "")
	{
		if($path != "") {
			$this->uploadPath = $path;
		}
		
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$folder = $this->makeDirectory($this->uploadPath);
		
		$result = [
			'success' => true,
			'data' => [],
		];
		
		foreach($files as $key => $value) {
			try {
				if ($value->getError() !== UPLOAD_ERR_OK) {
					throw new \Exception("Upload error ({$value->getError()}) for file: " . $value->getClientOriginalName());
				}				
				//put에서 파일명이 너무 길거나 파일 헤더의 확장자 정보를 제대로 못가져와서 bin타입으로 저장하는 버그가 발견되어 수정.
				//$realname = Storage::disk($this->uploadStore)->put($path, $value);
				$realname = Storage::disk($this->uploadStore)->putFileAs($path, $value, uniqid() . rand(1, 9999) . "." . $value->getClientOriginalExtension());
				array_push($result['data'], ['original_name' => $value->getClientOriginalName(), 'renamed_name' => $realname, 'key_name' => $key]);
			} catch(\Exception $e) {
				array_push($result, ['success' => false]);
				return $result;
			}
		}
		return $result;
	}
	
	public function namedUpload(object $files, String $path = "", String $store = "", String $fileName = "")
	{
		$newName = "tmp";
		
		if($path != "") {
			$this->uploadPath = $path;
		}
		
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		if($fileName != "") {
			$newName = $fileName;
		}
		
		$result = [
			'success' => true,
			'data' => [],
		];
		
		try {
			if ($files->getError() !== UPLOAD_ERR_OK) {
				throw new \Exception("Upload error ({$files->getError()}) for file: " . $files->getClientOriginalName());
			}
			$realname = Storage::disk($this->uploadStore)->putFileAs($path . "", $files, $newName . "." . $files->getClientOriginalExtension());
			array_push($result['data'], ['original_name' => $files->getClientOriginalName(), 'renamed_name' => $realname]);
		} catch(\Exception $e) {
			array_push($result, ['success' => false]);
			return $result;
		}
		
		return $result;
	}
	
	public function upload(object $files, String $path = "", String $store = "")
	{
		if($path != "") {
			$this->uploadPath = $path;
		}
		
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$result = [
			'success' => true,
			'data' => [],
		];
		
		try {
			if ($files->getError() !== UPLOAD_ERR_OK) {
				throw new \Exception("Upload error ({$files->getError()}) for file: " . $files->getClientOriginalName());
			}
			$realname = Storage::disk($this->uploadStore)->putFileAs($path, $files, uniqid() . rand(1, 9999) . "." . $files->getClientOriginalExtension());
			array_push($result['data'], ['original_name' => $files->getClientOriginalName(), 'renamed_name' => $realname]);
		} catch(\Exception $e) {
			array_push($result, ['success' => false]);
			return $result;
		}
		
		return $result;
	}
	
	public function deleteFile(String $file, String $store = "") 
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		try {
			Storage::disk($this->uploadStore)->delete($file);
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}
	
	public function moveFromTemporaryToNew(String $temp, String $real, String $store = "")
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$year = date("Y");
		$month = date("m");
		
		$this->makeDirectory($real);
		$this->makeDirectory($real . "/" . $year);
		$this->makeDirectory($real . "/" . $year . "/" . $month);
		
		$fileName = basename($temp);
		
		try {
			Storage::disk($this->uploadStore)->move($temp, $real . "/" . $year . "/" . $month . "/" . $fileName);
			return $real . "/" . $year . "/" . $month . "/" . $fileName;
		} catch(\Exception $e) {
			return $e->getMessage();
			return false;
		}
	}
	
	public function copyFromTemporaryToNew(String $temp, String $real, String $store = "")
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$year = date("Y");
		$month = date("m");
		
		$this->makeDirectory($real);
		$this->makeDirectory($real . "/" . $year);
		$this->makeDirectory($real . "/" . $year . "/" . $month);
		
		$fileName = basename($temp);
		
		try {
			Storage::disk($this->uploadStore)->copy($temp, $real . "/" . $year . "/" . $month . "/" . $fileName);
			return $real . "/" . $year . "/" . $month . "/" . $fileName;
		} catch(\Exception $e) {
			return $e->getMessage();
			return false;
		}
	}
	
	public function extractTo($file, String $path, String $store)
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$this->makeDirectory('tmp');
		$this->makeDirectory('tmp/' . $path);
		$zip = new \ZipArchive();
		$zip->open($file);
		$new_path = Storage::disk($this->uploadStore)->path('tmp/' . $path);
		$zip->extractTo($new_path);
		$zip->close();
		return [
			'path' => 'tmp/' . $path,
			'files' => $this->readDirectory($new_path),
		];
	}
	
	public function readFiles(String $path, String $store = "")
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		return Storage::disk($this->uploadStore)->allFiles($path);
	}
	
	public function readDirectory(String $path)
	{
		$files = [];
		$handle = opendir($path);
		while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
				array_push($files, $entry);
			}
		}
		closedir($handle);
		return $files;
	}
	
	public function moveFile(String $from, String $to)
	{
		Storage::disk($this->uploadStore)->move($from, $to);
		return $to;
	}
	
	public function fileCheck(String $store, String $path)
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		return Storage::disk($this->uploadStore)->exists($path);
	}

	protected function makeDirectory(String $path)
	{
		$folder = Storage::disk($this->uploadStore)->path($path);
		if(!File::isDirectory($folder)) {
			//$oldumask = umask(0);
			File::makeDirectory($folder, 0777, true, true);
			//umask($oldumask);
		}
	}

	private function deleteDirectory(String $path)
	{
		$folder = Storage::disk($this->uploadStore)->path($path);
		if(File::isDirectory($folder)) {
			File::deleteDirectory($folder);
		}
	}
	
	private function findDirectory(String $path, String $store = "")
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$folder = Storage::disk($this->uploadStore)->path($path);
		if(File::isDirectory($folder)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function download(String $path, String $store = "")
	{
		if($store != "") {
			$this->uploadStore = $store;
		}
		
		$ext = substr($path, strrpos($path, '.') + 1);
		
		try {
			return response()->download(storage_path('app/' . $path), "download." . $ext);
		} catch(\Exception $e) {
			abort(500);
		}
	}
}
