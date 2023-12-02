<?php

namespace App\Console\Commands;

use App\Http\Models\SendEmail;
use App\Mail\SendMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Log::info('mail calistiiii');
            $result = SendEmail::where('status',0)->get();
            if(empty($result)){
                return;
            }
            Log::info('mail calistiiii2222');

            foreach ($result as $item) {
                $mailData = [
                    'to' => $this->replace($item->to_email ?? ''),
                    'from' => $this->replace($item->from_email ?? ''),
                    'subject' => $item->konu,
                    'cc' => $this->replace($item->cc ?? ''),
                    'bcc' => $this->replace($item->bcc ?? ''),
                    'attachment' => $this->replace($item->ekler ?? ''),
                    'content' => $item->icerik,
                ];
                Mail::to($mailData['to'])->send(new SendMail($mailData));
                SendEmail::where('id',$item->id)->update('status',1);
            }
        }catch (\Exception $e){
            Log::error('mail error', ['message' => $e->getMessage()]);
        }


    }
    private function replace(string $string){
        return preg_replace('/\s\s+/', '', $string);
    }
}
