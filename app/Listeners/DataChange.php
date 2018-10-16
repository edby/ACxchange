<?php

namespace App\Listeners;

use App\Events\LoginAction;
use App\Tool\IpTool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DataChange
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param  LoginAction  $event
     * @return void
     */
    public function handle(LoginAction $event)
    {
        $event->user->old_ip = $event->user->login_ip;
        $event->user->old_login = strtotime($event->user->last_login);
        $event->user->login_ip = IpTool::get_client_ip();
        $event->user->last_login = time();
        $event->user->save();
    }
}
