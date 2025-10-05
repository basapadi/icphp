<?php
namespace App\Traits;

use App\Services\PythonRunner;
use Illuminate\Support\Facades\DB;

trait Services
{
  public function python(){
    return new PythonRunner();
  }
}