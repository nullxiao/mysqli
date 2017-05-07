<?php
//Created by NULL_XIAO
//使用Mysqli操作mysql数据库
define('HOST', 'localhost');
define('USER_NAME', 'root');
define('PASSWORD', '');
//连接mysql 并选择数据库
function getMysqli($dbName){
    $mysqli = new mysqli(HOST,USER_NAME,PASSWORD);
    $mysqli->set_charset('UTF8');
    $mysqli->select_db($dbName);
    return $mysqli;
}
/***************************************************/
//查询表中的内容
function select(){
    $mysqli = getMysqli('mytest');
    $sql = 'SELECT * FROM test';                            //SQL语句
    $result = $mysqli->query($sql);                          //返回查询的结果集  执行多条语句使用
    if($result && $result->num_rows > 0){                  //$result->num_rows 结果集中的行数
        foreach ($result as $content){                     //输出表中的内容
            echo $content['id'].'&nbsp;&nbsp;';
            echo $content['username'].'&nbsp;&nbsp;';
            echo $content['time'].'&nbsp;&nbsp;';
            echo "<hr/>";
        }
    }
    $mysqli->close();
    return $result;
}
function insert(){
    /***************************************************/
    //使用预处理往表内插入数据
    $mysqli = getMysqli('mytest');
    $sql = "INSERT test VALUES('default',?,?,?)";
    $usermae = '预处理语句';
    $content = "使用mysqli操作数据库";
    $time = date('Y-m-d H:i:s');
    $statement = $mysqli->prepare($sql);//准备预处理语句
    $statement->bind_param('sss',$usermae,$content,$time);//绑定参数
    $boo = $statement->execute();//执行预处理语句
    if($boo){
        echo '数据插入成功';
    }else{
        echo '数据插入失败';
    }
    $statement->free_result();
    $statement->close();
    $mysqli->close();
    /***************************************************/
}
//事务回滚 只要有一条sql语句失败 就不会修改数据
function rollback(){
    $mysqli = getMysqli('mytest');
    $mysqli->autocommit(false);//关闭自动提交
    $sql1 = "UPDATE test SET id = id + 5 WHERE username = '预处理语句' ";
    $sql2 = "UPDATE test SET id = id - 5 WHERE username = '中文'";
    $res1 = $mysqli->query($sql1);
    $row1 = $mysqli->affected_rows;//$mysqli->affected_rows 返回前一次MySQL操作所影响的记录行数。
    $res2 = $mysqli->query($sql2);
    $row2 = $mysqli->affected_rows;
    if($res1 && $res2 && $row1 > 0 && $row2 > 0){
        $mysqli->commit();//提交
        $mysqli->autocommit(true);//开启自动提交
        echo  '两条语句都执行成功';
    }else{
        $mysqli->rollback();//事务回滚
        echo 'sql语句执行失败';
    }
    $mysqli->close();
}
