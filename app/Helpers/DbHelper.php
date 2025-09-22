<?php
use Illuminate\Support\Facades\DB;

if(!function_exists('begin')){
	function begin(){
		DB::beginTransaction();
	}
}

if(!function_exists('commit')){
	function commit(){
		DB::commit();
	}
}

if(!function_exists('rollback')){
	function rollback(){
		DB::rollback();
	}
}