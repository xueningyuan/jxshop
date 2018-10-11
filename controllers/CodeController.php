<?php
namespace controllers;

class CodeController extends BaseController
{   
    public function create(){

        view("code/create");

    }
    // 生成代码
    public function make()
    {   
        $db = \libs\Db::make();
        $pri_list = $_POST['pri_name'];
        // 1. 接收参数（生成代码的表名）
        $tableName = $_POST['tableName'];
        $stmt = $db->prepare("SELECT id FROM privilege WHERE pri_name=?");
        $stmt->execute([
            $_POST['m_pri_name'].'模块'
        ]);
        $id = $stmt->fetch( \PDO::FETCH_ASSOC );
        
        if(!$id){
            $sql = "insert into privilege(pri_name,url_path,parent_id) values ('{$_POST['m_pri_name']}模块','',0)";
            $psm = $db->exec($sql);
            $id = $db->lastinsertid();
        }else{
            $id = $id['id'];
        }

        $sid = $id+1;

        $stmt = $db->prepare("SELECT id FROM privilege WHERE pri_name=?");
        $stmt->execute([
            $pri_list.'列表'
        ]);
        $li = $stmt->fetch( \PDO::FETCH_ASSOC );
        
        if(!$li){
            $ssql = "insert into privilege(pri_name,url_path,parent_id) values    
            ('{$pri_list}列表','{$tableName}/index',$id),
                ('添加{$pri_list}','{$tableName}/create,{$tableName}/insert',$sid),
                ('删除{$pri_list}','{$tableName}/delete',$sid),
                ('修改{$pri_list}','{$tableName}/edit,{$tableName}/update',$sid)";
            
            $psm = $db->exec($ssql);
        }else{
            echo "列表重复，不可重复添加";
            return;
        }

        // 取出这个表中所有的字段信息
        $sql = "SHOW FULL FIELDS FROM $tableName";
        
        // 预处理
        $stmt = $db->prepare($sql);
        // 执行 SQL
        $stmt->execute();
        // 取出数据
        $fields = $stmt->fetchAll( \PDO::FETCH_ASSOC );

        // 收集所有字段的白名单
        $fillable = [];
        foreach($fields as $v)
        {
            if($v['Field'] == 'id' || $v['Field'] == 'created_at')
                continue ;
            $fillable[] = $v['Field'];
        }
        $fillable = implode("','", $fillable);

        $mname = ucfirst($tableName);

        // 2. 生成控制器
        // 拼出控制名的名字
        $cname = ucfirst($tableName).'Controller';
        /*
        加载模板
        */
        ob_start();
        include(ROOT . 'templates/controller.php');
        $str = ob_get_clean();
        file_put_contents(ROOT.'controllers/'.$cname.'.php', "<?php\r\n".$str);

        // 3. 生成模型
        
        ob_start();
        include(ROOT . 'templates/model.php');
        $str = ob_get_clean();
        file_put_contents(ROOT.'models/'.$mname.'.php', "<?php\r\n".$str);

        // 4. 生成视图文件
        // 生成视图目录
        @mkdir(ROOT . 'views/'.$tableName, 0777);

        // echo '<pre>';
        // var_dump( $fields );

        // exit;

        // create.html
        ob_start();
        include(ROOT . 'templates/create.html');
        $str = ob_get_clean();
        file_put_contents(ROOT.'views/'.$tableName.'/create.html', $str);

        // edit.html
        ob_start();
        include(ROOT . 'templates/edit.html');
        $str = ob_get_clean();
        file_put_contents(ROOT.'views/'.$tableName.'/edit.html', $str);

        // index.html
        ob_start();
        include(ROOT . 'templates/index.html');
        $str = ob_get_clean();
        file_put_contents(ROOT.'views/'.$tableName.'/index.html', $str);

    }
}