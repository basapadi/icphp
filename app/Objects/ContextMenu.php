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
    public string $type; //redirect_url,confirm,form_dialog
    public string $apiUrl;
    public string $icon;
    public string $onClick = 'test'; //test,getFormDialog,tambahData,viewData,editData,hapusData,returData,undoData,confirmPopup
    public string $color;
    public string $formUrl;
    public string $data;
    public string $title;
    public string $message;
    public ?array $forms;

    
    public function __construct(string $name, string $label, $type = 'confirm')
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
    }
}