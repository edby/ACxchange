<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authy extends Model
{
    protected $table = 'authy';

    protected $fillable = ['user_id','country_code','phone_number','authy_id','verified','email'];

    protected $hidden = ['authy_id'];

    public function register_authy() {
        $authy_api = new AuthyApi(getenv('AUTHY_API_KEY'));
        $user = $authy_api->registerUser($this->email, $this->phone_number, $this->country_code);

        if($user->ok()) {
            $this->authy_id = $user->id();
            $this->save();
            return true;
        } else {
            // something went wrong
            return false;
        }
    }

    public function sendOneTouch($message) {
        // reset oneTouch status
        if($this->authy_status != 'unverified') {
            $this->authy_status = 'unverified';
            $this->save();
        }

        $params = array(
            'api_key'=>getenv('AUTHY_API_KEY'),
            'message'=>$message,
            'details[Email]'=>$this->email,
        );

        $defaults = array(
            CURLOPT_URL => "https://api.authy.com/onetouch/json/users/$this->authy_id/approval_requests",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $output = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($output);

        return $json;
    }

    public function sendToken() {
        $authy_api = new AuthyApi(getenv('AUTHY_API_KEY'));
        $sms = $authy_api->requestSms($this->authy_id);

        return $sms->ok();
    }

    public function verifyToken($token) {
        $authy_api = new AuthyApi(getenv('AUTHY_API_KEY'));
        $verification = $authy_api->verifyToken($this->authy_id, $token);

        if($verification->ok()) {
            return true;
        } else {
            return false;
        }
    }
}
