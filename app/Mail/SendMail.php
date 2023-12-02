<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'title' => $this->mailData['subject'],
            'content' => $this->mailData['content']
        ];
        $bcc = explode(',',$this->mailData['bcc']);
        $cc = explode(',',$this->mailData['cc']);
        $attachment = explode(',',$this->mailData['attachment']);
        Log::info('cc eeee',$cc);
        $obj =  $this->from($this->mailData['from'])
            ->subject($this->mailData['subject']);
        if(!empty($bcc)){
            //$obj->bcc($bcc);
        }
        if(!empty($cc)){
            $obj->cc($cc);
        }
        if(!empty($attachment)){
            foreach ($attachment as $attach) {
                $path = storage_path().'/app/'.$attach;

                if(file_exists($path)){
                    Log::info('storage',['url' => $path]);
                    $obj->attach($path);
                }
                //
            }
        }
        return $obj->view('emails.deneme', $data);
    }
}
