<?php
/**
 * Created by Code-Architect.
 * Date: 14-Oct-15
 * Time: 11:13 PM
 */

require_once('Mysql.php');
$db = new MysqlDB('localhost', 'root','','oophp');

$insertData = [
    'title' => 'Hello World',
    'body'  => 'The world is not nice at all mate!'
];

if($db->insert('posts', $insertData)) echo "success";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <?php //$results = $db->query('select * from posts'); ?>
    <?php
    //$db->where('id', 1);
    /*$results = $db->get('posts', 3);

    echo "<pre>";
    print_r($results);
    echo "</pre>";
*/
    ?>
</body>
</html>
