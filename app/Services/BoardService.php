<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Board;
use App\Models\BoardReply;
use App\Models\BoardFiles;

class BoardService 
{
	private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new BoardService();
        }

        return self::$instance;
    }
	
	public function registBoard(array $data)
	{
		return Board::insertGetId($data);
	}
	
	public function updateBoard(int $boardID, array $data)
	{
		return Board::where('id', $boardID)->update($data);
	}
	
	public function insertFiles(array $data)
	{
		return BoardFiles::insert($data);
	}
	
	public function boardTotal()
	{
		return Board::count();
	}
	
	public function boardFilteredTotal(String $search)
	{
		$board = Board::where('id', '>', 0);
		
		if($search != '') {
			$board = $board->where(function($where) use($search) {
				$where->where('writer', 'like', '%' . $search . '%')
					->orWhere('subject', 'like', '%' . $search . '%');
			});
		}
		
		return $board->count();
	}
	
	public function boardFilteredList(int $skip, int $take, String $search)
	{
		$board = Board::where('id', '>', 0);
		
		if($search != '') {
			$board = $board->where(function($where) use($search) {
				$where->where('writer', 'like', '%' . $search . '%')
					->orWhere('subject', 'like', '%' . $search . '%');
			});
		}
		
		return $board->skip($skip)->take($take)->orderBy('id', 'desc')->get();
	}
	
	public function getBoard(int $id)
	{
		return Board::where('id', $id)->first();
	}
	
	public function getFiles(int $id)
	{
		return BoardFiles::where('board_num', $id)->get();
	}
	
	public function getFile(int $id)
	{
		return BoardFiles::where('id', $id)->first();
	}
	
	public function deleteFile(int $id)
	{
		return BoardFiles::where('id', $id)->delete();
	}
	
	public function replyTotal(int $id)
	{
		return BoardReply::where('board_id', $id)->count();
	}
	
	public function getReply(int $id, int $skip, int $take)
	{
		$sql = "
			WITH RECURSIVE ReplyHierarchy AS ( 
				SELECT id, board_id, writer, user_id, reply, parent_id, depth, censorship, created_at, updated_at, id AS root_id, 0 AS level 
				FROM board_reply WHERE parent_id = 0 and board_id = " . $id . "
			UNION ALL 
				SELECT r.id, r.board_id, r.writer, r.user_id, r.reply, r.parent_id, r.depth, r.censorship, r.created_at, r.updated_at, h.root_id, h.level + 1 
				FROM board_reply r INNER JOIN ReplyHierarchy h ON r.parent_id = h.id where r.board_id = " . $id . "
			) 
			SELECT id, board_id, writer, user_id, reply, parent_id, depth, censorship, created_at, updated_at 
			FROM ReplyHierarchy ORDER BY root_id DESC, level, depth ASC LIMIT " . $take . " OFFSET " . $skip;
			
		return DB::select($sql);
	}
	
	public function setReply(array $data)
	{
		return BoardReply::create($data);
	}
	
	public function censorReply(int $id)
	{
		return BoardReply::where('id', $id)
						->update(
							[
								'censorship' => DB::raw('if(censorship="Y", "N", "Y")'),
							]
						);
	}
	
	public function deleteReply(int $id)
	{
		return BoardReply::where('id', $id)->delete();
	}
}
