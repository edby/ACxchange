<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class PasswordReset extends Mailable
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
        //subject 默认为 类名
//        $imgPath =storage_path('app/img/email_head.png');
//        ->attach($docPath,['as'=>"=?UTF-8?B?".base64_encode('测试文档')."?=.docx"])
//        $docPath = storage_path('app/file/test.docx') ;
        return $this->view('front.email')
            ->with([
                'email' => $this->emailData['email'],
                'code' => $this->emailData['message'],
                'text'=>$this->emailData['text'],
                ])->subject($this->emailData['title']);
    }
}
