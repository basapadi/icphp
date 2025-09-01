<?php
namespace App\Objects;

/**
 * Mengatur data di merge ke data rows
 *
 * @param string $attribute sebagai nama attribute di data row 
 * @param string $class classname yang merupakan sumber data yang akan di merge
 * @param string $key attribute data yang merupakan key relasi
 * @param array $relations relasi yang dibutuhkan untuk diload di data yang dimerge
 * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
 */
class MergeData
{
    public string $attribute;
    public string $key;
    public string $class;
    public ?DataArray $whereNotIn;
    public ?DataArray $whereIn;
    public array $relations = [];

    
    public function __construct(
        ?string $attribute = '', 
        ?string $class = '', 
        ?string $key = '', 
        ?array $relations = [],
        ?DataArray $whereNotIn = null,
        ?DataArray $whereIn = null
        )
    {
        $this->attribute = $attribute;
        $this->key = $key;
        $this->class = $class;
        $this->relations = $relations;
        $this->whereNotIn = $whereNotIn;
        $this->whereIn = $whereIn;

    }
}