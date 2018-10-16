<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/12
 * Time: 12:04
 */

namespace App\Http\Controllers\FrontEnd;


use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RedirectsUsers;

class HelpController extends Controller
{
    use RedirectsUsers;

    protected $redirectTo = '/login';

    public function index()
    {
        return view('front.help');
    }

    public function contactUs()
    {
        return view('front.contact');
    }

    public function en()
    {
        return view('front.apiEn');
    }

    public function cn()
    {
        return view('front.apiCh');
    }
}