<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasQueryBuilder
{
    /**
     * Tambahkan eager load relasi ke query builder.
     */
    public function with(array $relations)
    {
        $this->_queryBuilder = $this->_queryBuilder->with($relations);
        return $this; // supaya bisa chaining
    }

    /**
     * Tambahkan groupBy ke query builder.
     */
    public function groupBy($columns)
    {
        $this->_queryBuilder = $this->_queryBuilder->groupBy($columns);
        return $this;
    }

    public function orderBy($column='id',$sort = 'desc')
    {
        $this->_queryBuilder = $this->_queryBuilder->orderBy($column,$sort);
        return $this;
    }

    /**
     * Setter manual query builder (kalau butuh override).
     */
    public function setQuery(Builder $builder)
    {
        $this->_queryBuilder = $builder;
        return $this;
    }
}
