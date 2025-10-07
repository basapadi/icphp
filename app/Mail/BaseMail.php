<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Traits\BaseHelper;

class BaseMail extends Mailable
{
	 use Queueable, SerializesModels, BaseHelper;
}