<?php
include(__DIR__ . DIRECTORY_SEPARATOR . 'app.php');
$app = new App();
$app->init();


$post = ($_POST);
if($post){
    $dp = new \common\models\DisplayPicture();
    $dp->generate();
}
?>

<form method='post'><input type='hidden' name='_generate'><button>generate</button></form>
