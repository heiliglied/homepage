<?php

namespace App\Extra;

use App\Models\jobCheck;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Extra\FileLibrary;

class JobLib
{
	public function __construct() {}
	
	public function createData(String $jobkey)
	{
		try {
			jobCheck::create(
				[
					'jobkey' => $jobkey,
					'status' => 'I',
				]
			);
			
			//Job으로 넘기는 수밖에 없음.
			//$this->createExcel($jobkey);
			
			return [
				'status' => 'success',
				'msg' => 'Job 시작',
			];
		} catch(Exception $e) {
			return [
				'status' => 'error',
				'msg' => 'Job 시작 실패',
			];
		}
	}
	
	public function jobCheck(String $jobkey)
	{
		$job = jobCheck::where('jobkey', $jobkey)->first();
		if(empty($job)) {
			return [
				'status' => 'fail',
				'data' => [],
			];
		} else {
			return [
				'status' => 'success',
				'data' => $job->toArray(),
			];
		}
	}
	
	public function deleteJob(String $jobkey)
	{
		jobCheck::where('jobkey', $jobkey)->delete();
		
		return [
			'status' => 'success',
			'msg' => '정상 삭제',
		];
	}
	
	private function createExcel(String $jobkey)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		
		$sheet->setCellValue('A1', 'No.');
		
		for($i = 2; $i < 22; $i++) {
			$sheet->setCellValue();
			sleep(1);
		}
		
		return response()->download(storage_path('app/' . $file->renamed_name), $file->original_name);
		
		$sheet->getColumnDimension('A')->setAutoSize(true);
		
		$date = date('Y-m');
		$yearmonth = explode('-', $date);		
		$path = 'temps/' . $yearmonth[0] . '/' . $yearmonth[1];
		
		$file_path = storage_path('app/' . $path . '/' . $jobkey . '.xlsx');
		
		$writer = new Xlsx();
		$writer->save($file_path);
		
		jobCheck::where('jobkey', $jobkey)->update(
			[
				'status' => 'E',
				'return' => $file_path,
			]
		);
	}
}