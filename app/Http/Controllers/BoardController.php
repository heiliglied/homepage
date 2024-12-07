<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Extra\BoardLib;

class BoardController extends Controller
{
    public function __construct() {}
	
	public function board(Request $request)
	{
		return view('board.list');
	}
	
	public function write(Request $request)
	{
		return view('board.write', ['boardID' => 0, 'uploadType' => 'create']);
	}
	
	public function imageUpload(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->uploadImage($request->upload);
	}
	
	public function regist(Request $request)
	{
		$data = [
			'type' => $request->type,
			'boardID' => $request->boardID,
			'subject' => $request->subject,
			'contents' => $request->contents,
			'file' => $request->file('files'),
		];
		
		$BoardLib = new BoardLib();
		return $BoardLib->regist($data);
	}
	
	public function boardList(Request $request) 
	{
		$params = [
			'page' => $request->page ?? 1,
			'search' => $request->search ?? '',
		];
		
		$BoardLib = new BoardLib();
		return $BoardLib->boardList($params);
	}
	
	public function modify(Request $request)
	{
		$BoardLib = new BoardLib();
		$modifyCheck = $BoardLib->modifyCheck($request->id);
		
		if($modifyCheck == 'fail') {
			abort(503);
		}
		
		return view('board.write', ['boardID' => $request->id, 'uploadType' => 'update']);
	}
	
	public function getBoard(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->getBoard($request->id ?? 0);
	}
	
	public function downloadFile(Request $request)
	{
		$BoardLib = new BoardLib();
		$file = $BoardLib->getFile($request->id);
		
		return response()->download(storage_path('app/' . $file->renamed_name), $file->original_name);
	}
	
	public function deleteFile(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->deleteFile($request->id);
	}
	
	public function boardView(Request $request)
	{
		return view('board.view', ['boardID' => $request->id]);
	}
	
	public function getView(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->getView($request->id);
	}
	
	public function censorBoard(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->censorBoard($request->id);
	}
	
	public function deleteBoard(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->deleteBoard($request->id);
	}
	
	public function setReply(Request $request)
	{
		$data = [
			'boardID' => $request->boardID,
			'contents' => $request->contents,
		];
		
		$BoardLib = new BoardLib();
		return $BoardLib->setReply($data);
	}
	
	public function replyCensor(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->reCensorBoard($request->id);
	}
	
	public function deleteReply(Request $request)
	{
		$BoardLib = new BoardLib();
		return $BoardLib->deleteReply($request->id);
	}
	
	public function getReply(Request $request)
	{
		$params = [
			'page' => $request->page ?? 1,
			'boardID' => $request->boardID,
		];
		
		$BoardLib = new BoardLib();
		return $BoardLib->getReply($params);
	}
}
