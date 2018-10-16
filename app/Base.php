<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/17
 * Time: 10:00
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    /**
     * 查找一个数据 用数组where
     * @param array  $where
     * @param array $field
     * @return array
     */
    public function findArr($where,$field = ['*'])
    {
        return $this->where($where)->first($field);
    }

    /**
     * 查找一个数据 用字符串
     * @param string  $field
     * @param string $symbol
     * @param mixed $value
     * @param array $fieldArr
     * @return array
     */
    public function findString($field,$symbol,$value = false,$fieldArr = ['*'])
    {
        if (false === $value){
            $this->where($field,$symbol);
        }else{
            $this->where($field,$symbol,$value);
        }
        return $this->first($fieldArr);
    }

}