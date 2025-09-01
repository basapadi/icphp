<?php
namespace App\Objects;

/**
 * Data string
 * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
 */
class DataString
{
    public string $key;
    public string $value;

    
    public function __construct(
        string $key, 
        string $value, 
        )
    {
        $this->value = $value;
        $this->key = $key;

    }
}