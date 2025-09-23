<?php
namespace App\Objects;

/**
 * ContextMenu Grid
 * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
 */
class ContextMenu
{
    public string $label;
    public array  $conditions;
    public string $module;
    public string $name;
    public string $type; //redirect_url,confirm,dialog
    public string $apiUrl;
    public string $icon;
    public string $onClick = 'test';
    public string $color;

    
    public function __construct(string $name, string $label, $type = 'confirm')
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
    }
}