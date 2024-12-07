<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\JsTester;

class TesterService 
{
	private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new TesterService();
        }

        return self::$instance;
    }
	
	public function save(array $data)
	{
		return JsTester::create($data);
	}
	
	public function getDuplicateCode(String $code)
	{
		return JsTester::where('page_key', $code)->count();
	}
	
	public function getTesterList(String $column, String $value, int $skip, int $take)
	{
		return JsTester::where($column, $value)->skip($skip)->take($take)->get();
	}
	
	public function getTesterCount(String $column, String $value)
	{
		return JsTester::where($column, $value)->count();
	}
	
	public function getTesterCode(int $id)
	{
		return JsTester::where('id', $id)->first();
	}
	
	public function deleteTesterCode(int $id)
	{
		return JsTester::where('id', $id)->delete();
	}
}