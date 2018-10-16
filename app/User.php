<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    public static $authType = [
        1 => 'googleAuth',
        2 => 'authyVerification',
        3 => 'SMSVerification',
    ];

    protected $guarded = ['email_confirmation','password_confirmation','captcha','check','step'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 设定用户的密码。
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * 设置交易密码
     * @param $value
     * @return  void
     */
    public function setPinAttribute($value)
    {
        $this->attributes['pin'] = Hash::make($value);
    }

    /**
     * 邮箱不区分大小写
     * @param $value
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * 获取时间转换
     * @param string $value
     * @return false|string
     */
    public function getOldLoginAttribute($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    /**
     * 获取时间转换
     * @param string $value
     * @return false|string
     */
    public function getLastLoginAttribute($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    /**
     * 获得与用户关联的身份证信息
     */
    public function IdCards()
    {
        return $this->hasOne('App\IdCard','id', 'card_id');
    }

    /**
     * 获得与用户关联的护照信息
     */
    public function Passports()
    {
        return $this->hasOne('App\Passport','id', 'passport_id');
    }

    /**
     * 获取用的币种
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCurr()
    {
        return $this->hasMany('App\UserCurr');
    }

    /**
     * 获取用户的订单细节
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail()
    {
        return $this->hasMany('App\OrderDetail','user_id','id');
    }
}
