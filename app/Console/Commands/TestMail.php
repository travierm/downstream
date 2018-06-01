<?php

namespace App\Console\Commands;

use Mail;
use Illuminate\Console\Command;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {from} {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending';

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
        $from = $this->argument('from');
        $to = $this->argument('to');

        Mail::raw("test from downstream instance", function($message) use($from, $to) {
            $message->from($from);
            $message->to($to, 'Downstream User');
            $message->subject("Test from Downstream Instance");
        });

        if (Mail::failures()) {
            $this->error("Failed to send mail!");
        }else{
            $this->info("Mail sent successfully!");
        }
    }
}
