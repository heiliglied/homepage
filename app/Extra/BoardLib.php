<?php

namespace App\Extra;
use App\Extra\FileLibrary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\BoardService;
use App\Traits\CommonTrait;

class BoardLib
{
	use CommonTrait;
	
	public function __construct() {}
	
	public function uploadImage($file) 
	{
		//용량 체크. 3mb까지
		if($file->getSize() > 3145728) {
			return json_encode(
				[
					'error' => [
						'message' => '업로드 가능한 용량은 3mb까지 입니다.'
					]
				]
			);
		}
		
		$date = date('Y-m');
		$yearmonth = explode('-', $date);		
		$path = 'temps/' . $yearmonth[0] . '/' . $yearmonth[1];
		
		$FileLibrary = new FileLibrary();
		$result = $FileLibrary->upload($file, $path, 'custom');
		
		if($result['success']) {
			return json_encode(
				[
					'url' => '/storage/' . $result['data'][0]['renamed_name']
				]
			);
		} else {
			return json_encode(
				[
					'error' => [
						'message' => '이미지 등록에 실패하였습니다.'
					]
				]
			);
		}
	}
	
	public function regist(array $data)
	{
		$files = "";
		
		if(!empty($data['file'])) {
			if(count($data['file']) > 5) {
				return [
					'status' => 'error',
					'msg' => '최대 업로드 갯수를 초과하였습니다.'
				];
			}
			
			$file_chk = $this->fileValidator($data['file']);
			if($file_chk->fails()) {
				return [
					'status' => 'error',
					'msg' => '업로드 용량을 초과하였습니다.'
				];
			}
			$files = "Y";
		}
		
		DB::beginTransaction();
		try {
			$moveImage = $this->moveImagePath($data['contents']);
			if($moveImage['status'] == 'error') {
				return [
					'status' => 'error',
					'msg' => '작성에 실패하였습니다.'
				];
			}
			
			$time = \Carbon\Carbon::now();
			
			$board = [
				'writer' => Auth::user()->name,
				'user_id' => Auth::user()->id,
				'subject' => $data['subject'],
				'contents' => $moveImage['data'],
			];
			
			$BoardService = BoardService::getInstance();
			$board_id = 0;
			
			if($data['type'] == 'create') {
				$board['created_at'] = $time->toDateTimeString();
				$board['updated_at'] = $time->toDateTimeString();
				$regist_id = $BoardService->registBoard($board);
				$board_id = $regist_id;
			} else {
				$board['updated_at'] = $time->toDateTimeString();
				$BoardService->updateBoard($data['boardID'], $board);
				$board_id = $data['boardID'];
			}
			
			if($files == 'Y') {
				$fileLibrary = new FileLibrary();
				$uploadedFile = $fileLibrary->multiUpload($data['file'], 'files/' . $time->year . '/' . $time->month, 'upload');
				$boardFiles = [];
				foreach($uploadedFile['data'] as $value) {
					array_push($boardFiles, 
						[
							'board_num' => $board_id,
							'original_name' => $value['original_name'],
							'renamed_name' => $value['renamed_name'],
							'created_at' => $time->toDateTimeString(),
							'updated_at' => $time->toDateTimeString(),
						]
					);
				}
				$BoardService->insertFiles($boardFiles);
			}
			DB::commit();
			
			return [
				'status' => 'success',
				'msg' => '작성되었습니다.'
			];
			
		} catch(\Exception $e) {
			DB::rollback();
			return [
				'status' => 'error',
				'msg' => '작성에 실패하였습니다.'
			];
		}	
	}
	
	public function boardList(array $search)
	{
		$page = $search['page'] ? $search['page'] : 1;
		
		$pageSize = 10;
		$showPages = 10;
		
		$skip = ($page - 1) * $pageSize;
		
		$BoardService = BoardService::getInstance();
		
		$boardTotal = $BoardService->boardTotal();
		$boardFilteredTotal = $BoardService->boardFilteredTotal($search['search']);
		$boardFilteredList = $BoardService->boardFilteredList($skip, $pageSize, $search['search']);
		
		$parameters = [
			'totalCount' => $boardTotal,
			'pageSize' => $pageSize,
			'showPages' => $showPages,
			'currentPage' => $page,
			'skip' => true,
		];
		
		$pageList = $this->pagination($parameters);
		
		return [
			'total' => $boardTotal,
			'filteredTotal' => $boardFilteredTotal,
			'filteredList' => $boardFilteredList,
			'pageList' => $pageList,
		];
	}
	
	private function moveImagePath(String $contents)
	{
		$replace_contents = $contents;
		preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $contents, $matches);
		
		$FileLibrary = new FileLibrary();
		
		try {
			foreach($matches[1] as $value) {
				//values => /storage/temps/~~~
				$new_value = str_replace('/storage/temps/', '/temps/', $value);
				$move_path = $FileLibrary->copyFromTemporaryToNew($new_value, 'images', 'custom');
				$move_path_replace = str_replace(env('SERVER_ROOT_DIR') . '/storage/app/public', '', $move_path);
				$replace_contents = str_replace($value, '/storage/' . $move_path_replace, $replace_contents);
			}
			
			return [
				'status' => 'success',
				'data' => $replace_contents,
			];
			
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'data' => '',
			];
		}
	}
	
	protected function fileValidator(array $data)
    {
        return Validator::make($data, [
            'files.*' => ['max:10240'],
        ], ['files.*.max' => '업로드 용량을 초과하였습니다.']);
    }
	
	public function modifyCheck(int $id)
	{
		$BoardService = BoardService::getInstance();
		$board = $BoardService->getBoard($id);
		if((Auth::user()->roll <= 1) || (Auth::user()->id == $board->user_id)) {
			return 'success';
		} else {
			return 'fail';
		}
	}
	
	public function getBoard(int $id)
	{
		$boardData = [];
		$fileData = [];
		
		$BoardService = BoardService::getInstance();
		$board = $BoardService->getBoard($id);
		$files = $BoardService->getFiles($id);
		
		if(!empty($board) > 0) {
			$boardData = $board->toArray();
		}
		
		if(count($files) > 0) {
			$fileData = $files->toArray();
		}
		
		return [
			'board' => $boardData,
			'files' => $fileData,
		];
	}

	public function getFile(int $id)
	{
		$BoardService = BoardService::getInstance();
		$file = $BoardService->getFile($id);
		
		return $file;
	}
	
	public function deleteFile(int $id)
	{
		$BoardService = BoardService::getInstance();
		$file = $BoardService->getFile($id);
		
		try {
			$BoardService->deleteFile($id);
			$remain_files = $BoardService->getFiles($file->board_num);
			return [
				'status' => 'success',
				'msg' => '삭제되었습니다.',
				'files' => $remain_files->toArray(),
			];
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '삭제에 실패하였습니다',
				'files' => [],
			];
		}
	}
	
	public function getView(int $id)
	{
		$page = 1;
		$showPages = 5;
		$pageSize = 20;
		$skip = ($page - 1) * $pageSize;
		
		$BoardService = BoardService::getInstance();
		$board = $BoardService->getBoard($id);
		$files = $BoardService->getFiles($id);
		$reply_total = $BoardService->replyTotal($id);
		$reply = $BoardService->getReply($id, $skip, $pageSize);
		$permission = 'N';
		if(Auth::user()->roll <= 1 || Auth::user()->id == $board->user_id) {
			$permission = 'Y';
		}
		
		$parameters = [
			'totalCount' => $reply_total,
			'pageSize' => $pageSize,
			'showPages' => $showPages,
			'currentPage' => $page,
			'skip' => true,
		];
		
		$pageList = $this->pagination($parameters);
		
		return [
			'board' => $board->toArray(),
			'permission' => $permission,
			'files' => empty($files) ? [] : $files->toArray(),
			'reply' => empty($reply) ? [] : json_decode(json_encode($reply), true),
			'reply_total' => $reply_total,
			'reply_paging' => $pageList,
			'admin' => Auth::user()->roll <= 1 ? 'Y' : 'N',
			'user_id' => Auth::user()->id ?? 0,
		];
	}
	
	public function censorBoard(int $id)
	{	
		$BoardService = BoardService::getInstance();
		
		try {
			$BoardService->updateBoard($id, ['censorship' => 'Y']);
			return [
				'status' => 'success',
				'msg' => '검열되었습니다.',
			];
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '검열에 실패하였습니다.',
			];
		}
	}
	
	public function reCensorBoard(int $id)
	{
		$BoardService = BoardService::getInstance();
		
		try {
			$BoardService->censorReply($id);
			return [
				'status' => 'success',
				'msg' => '검열내역이 수정 되었습니다.',
			];
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '검열에 실패하였습니다.',
			];
		}
	}
	
	public function deleteBoard($id)
	{	
		$BoardService = BoardService::getInstance();
		$files = $BoardService->getFiles($id);
		
		DB::beginTransaction();
		try {
			return [
				'status' => 'success',
				'msg' => '삭제 되었습니다.',
			];
		} catch(\Exception $e) {
			DB::rollback();
			return [
				'status' => 'false',
				'msg' => '삭제에 실패하였습니다',
			];
		}
	}
	
	public function setReply(array $data)
	{
		$BoardService = BoardService::getInstance();
		
		try {
			$moveImage = $this->moveImagePath($data['contents']);
			if($moveImage['status'] == 'error') {
				return [
					'status' => 'error',
					'msg' => '작성에 실패하였습니다.'
				];
			}
			
			$time = \Carbon\Carbon::now();
			
			$reply = [
				'writer' => Auth::user()->name,
				'user_id' => Auth::user()->id,
				'board_id' => $data['boardID'],
				'reply' => $moveImage['data'],
				'parent_id' => 0,
				'depth' => 0
			];
			
			$BoardService->setReply($reply);
	
			return [
				'status' => 'success',
				'msg' => '작성되었습니다.'
			];
	
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '작성에 실패하였습니다.'
			];
		}
	}
	
	public function deleteReply(int $id)
	{
		$BoardService = BoardService::getInstance();
		
		try {
			$BoardService->deleteReply($id);
			
			return [
				'status' => 'success',
				'msg' => '삭제되었습니다..'
			];
		} catch(\Exception $e) {
			return [
				'status' => 'error',
				'msg' => '삭제에 실패하였습니다.'
			];
		}
	}
	
	public function getReply(array $search)
	{
		$page = $search['page'] ? $search['page'] : 1;
		
		$pageSize = 20;
		$showPages = 5;
		
		$skip = ($page - 1) * $pageSize;
		
		$BoardService = BoardService::getInstance();
		
		$replyTotal = $BoardService->replyTotal($search['boardID']);
		$replyFilteredTotal = $BoardService->replyTotal($search['boardID']);
		$replyFilteredList = $BoardService->getReply($search['boardID'], $skip, $pageSize);
		
		$parameters = [
			'totalCount' => $replyTotal,
			'pageSize' => $pageSize,
			'showPages' => $showPages,
			'currentPage' => $page,
			'skip' => true,
		];
		
		$pageList = $this->pagination($parameters);
		
		return [
			'total' => $replyTotal,
			'filteredTotal' => $replyFilteredTotal,
			'filteredList' => $replyFilteredList,
			'pageList' => $pageList,
		];
	}
}

/*
$ideaBoardService = IdeaBoardService::getInstance();
		$fileService = FileService::getInstance();
		$ideaReplyService = IdeaReplyService::getInstance();
		//$ideaBoard = $ideaBoardService->getOneRow('id', $request->id);
		$files = $fileService->getList($condition);
		$replies = $ideaReplyService->filteredCount('idea_board', $request->id);
		
		if($replies > 0) {
			try {
				DB::beginTransaction();
				$fileService->deleteList($condition);
				$ideaBoardService->update('id', $request->id, ['subject' => '삭제된 글입니다.', 'contents' => '', 'censorship' => 'N', 'deleted_at' => \Carbon\Carbon::now()]);
				DB::commit();
			} catch(\Exception $e) {
				abort(500);
			}
			
			$fileLibrary = new FileLibrary();
			foreach($files as $value) {
				$fileLibrary->deleteFile($value->renamed_name);
			}
		} else {
			try {
				DB::beginTransaction();
				$fileService->deleteList($condition);
				$ideaBoardService->delete(['id' => $request->id]);
				DB::commit();
			} catch(\Exception $e) {
				abort(500);
			}
			
			$fileLibrary = new FileLibrary();
			foreach($files as $value) {
				$fileLibrary->deleteFile($value->renamed_name);
			}
		}
		return redirect('/ideaBoard/list');
		*/