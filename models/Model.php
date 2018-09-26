<?php
namespace models;

use PDO;

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
        foreach($this->data as $k => $v)
        {
            $keys[] = $k;
            $values[] = $v;
            $token[] = '?';
        }
        $keys = implode(',', $keys);
        $token = implode(',', $token);   // ?,?,?,?
        $sql = "INSERT INTO {$this->table}($keys) VALUES($token)";

        // echo $sql;die;
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

    public function update($id)
    {
        $set = [];
        $token = [];

        foreach($this->data as $k => $v)
        {
            $set[] = "$k=?";
            $values[] = $v;
            $token[] = '?';
        }

        $set = implode(',', $set);

        $values[] = $id;

        $sql = "UPDATE {$this->table} SET $set WHERE id=?";

        $stmt = $this->_db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->_db->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function findAll($options = [])
    {
        $_option = [
            'fields' => '*',
            'where' => 1,
            'order_by' => 'id',
            'order_way' => 'desc',
            'per_page'=>20,
        ];

        // 合并用户的配置
        if($options)
        {
            $_option = array_merge($_option, $options);
        }

        /**
         * 翻页
         */
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page-1)*$_option['per_page'];
        
        $sql = "SELECT {$_option['fields']}
                 FROM {$this->table}
                 WHERE {$_option['where']} 
                 ORDER BY {$_option['order_by']} {$_option['order_way']} 
                 LIMIT $offset,{$_option['per_page']}";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll( PDO::FETCH_ASSOC );

        /**
         * 获取总的记录数
         */
        $stmt = $this->_db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$_option['where']}");
        $stmt->execute();
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        $pageCount = ceil($count/$_option['per_page']);

        $page_str = '';
        for($i=1;$i<=$pageCount;$i++)
        {
            $page_str .= '<a href="?page='.$i.'">'.$i.'</a> ';
        }

        return [
            'data' => $data,
            'page' => $page_str,
        ];
    }

    public function findOne($id)
    {
        $stmt = $this->_db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }
 }