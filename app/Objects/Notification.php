<?php
namespace App\Objects;

/**
 * Notification
 * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
 */
class Notification
{
    public string $title;
    public string $message;
    public string $reference;
    public string $reply;
    public array $actions;

    
    public function __construct( string $title,  string $message, string $reference = '', string $reply = '', array $actions = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->reference = $reference;
        $this->reply = $reply;
        $this->actions = $actions;

    }
}