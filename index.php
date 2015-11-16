<?php
/**
 * Created by Code-Architect.
 * Date: 14-Oct-15
 * Time: 11:13 PM
 */

require_once('Mysql.php');
$db = new MysqlDB('localhost', 'root','','oophp');
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <?php $results = $db->query('select * from posts'); ?>
    <?php
    foreach($results as $row){
        echo '<p><h2>'.$row['title'].'</h2></p>';
        echo $row['body'];
    }
    ?>
</body>
</html>
