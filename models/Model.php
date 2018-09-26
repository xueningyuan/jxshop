<?php
namespace models;

/**
 * 所有模型的父模型，
 * 在这里实现所有表的：添加、修改、删除、查询翻页等功能
 */

 class Model
 {
    protected $_db;

    // 操作的表名，值由子类设置
    protected $table;
    // 表单中的数据，值由控制器设置
    protected $data;    

    /**
     * $data = [
     *   'title'=>'xxxx',
     *   'content' => 'xxxx',
     *   'is_show'=> 'y'
     * ];
     */

     public function __construct()
     {
        $this->_db = \libs\Db::make();
     }

     public function insert()
     {
        $keys=[];
        $values=[];
        $token=[];
        foreach($data as $k => $v)
        {
            $keys[] = $k;
            $values[] = $v;
            $token[] = '?';
        }
        $keys = implode(',', $keys);
        $token = implode(',', $token);   // ?,?,?,?
        $sql = "INSERT INTO {$this->table}($keys) VALUES($token)";
        $stmt = $this->_db->prepare($sql);
        return $stmt->execute($values);
     }

     // 接收表单中的数据
     public function fill($data)
     {
        // 判断是否在白名单中
        foreach($data as $k => $v)
        {
            if(!in_array($k, $this->fillable))
            {
                unset($data[$k]);
            }
        }
        $this->data = $data;
     }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function findAll()
    {

    }

    public function findOne()
    {

    }
 }