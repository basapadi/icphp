<?php
namespace App\Objects;

/**
 * Notification
 * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
 */
class ContextMenu
{
    public string $label;
    public array $conditions;
    public string $module;
    public string $name;
    public string $type; //redirect_url,confirm,dialog
    public string $apiUrl;

    
    public function __construct(string $name, string $label, $type = 'confirm',$apiUrl = '')
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->apiUrl = $apiUrl;
    }
}