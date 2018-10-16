<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/8
 * Time: 10:58
 */

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotiAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     * @param  array $emailData
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('front.notiadmin')->with($this->emailData);
    }
}