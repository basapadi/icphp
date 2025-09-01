<?php
namespace App\Objects;

/**
 * Data array
 * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
 */
class DataArray
{
    public string $key;
    public array $values;

    
    public function __construct( string $key,  array $values)
    {
        $this->values = $values;
        $this->key = $key;

    }
}