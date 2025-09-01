<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait QueryHelper
{
    /**
     * Buat DB::raw() tanpa harus import DB di controller.
     */
    protected function raw($expression)
    {
        return DB::raw($expression);
    }

    /**
     * Contoh method chaining untuk groupBy dengan raw expression
     */
    public function groupByRaw($expression)
    {
        $this->_queryBuilder = $this->_queryBuilder->groupBy(DB::raw($expression));
        return $this;
    }
}