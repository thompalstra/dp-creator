<?php
include(__DIR__ . DIRECTORY_SEPARATOR . 'app.php');
$app = new App();
$app->run();
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$url = substr($uri_parts[0], 1, strlen($uri_parts[0]));


if($url == 'download' && isset($_GET['file']) && file_exists('generated/' . $_GET['file'])){
    $filename = 'generated/' . $_GET['file']; // of course find the exact filename....
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false); // required for certain browsers
    header('Content-Type: application/pdf');

    header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    exit;
}

$generated = false;
$img = "";
$dp = new \common\models\DisplayPicture();
if($_POST && $dp->load($_POST) && $dp->validate()){
    $img = $dp->generate();
    $generated = true;

}
?>
<style>
    .form-label{
         float: left;
         width: 33.333334%;
    }
    .form-input{
        float: left;
        width: 33.333334%;
    }

    img.result{
        max-width: 100%;
        max-height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
    }
    .result-column,
    .form-column{
        width: 50%; float: left; position: relative; padding-bottom: 50%;
        background-color: #111
    }
    .inner{
        position: absolute; top: 20px; left: 20px; right: 20px; bottom: 20px;
    }
    .form-column{
        background-color: #333;
    }
</style>
<div style='width: 100%'>
    <div class='result-column'>
        <div class='inner'>
            <?php
            if($generated === true){
                echo "<img class='result' src='/generated/$img'></img>";
                echo "<a href='/download?file=$img'>download</a>";
            }
            ?>
        </div>

    </div>
    <div class='form-column'>
        <div class='inner'>
            <form method='post' style='color: white;'>
                <input type='hidden' name='_generate'>
                <div>
                    <?=$dp->outputErrors('padding')?>
                    <label class='form-label'>padding</label>
                    <input class='form-input' type='' name='DisplayPicture[padding]' placeholder='20'  value="<?=(!empty($dp->padding) ? $dp->padding : '')?>"></input>
                    <label class='form-label'>px</label>
                </div>
                <div>
                    <?=$dp->outputErrors('width')?>
                    <label class='form-label'>width</label>
                    <input class='form-input' type='' name='DisplayPicture[width]' placeholder='512'  value="<?=(!empty($dp->width) ? $dp->width : '')?>"></input>
                    <label class='form-label'>px</label>
                </div>
                <div>
                    <?=$dp->outputErrors('height')?>
                    <label class='form-label'>height</label>
                    <input class='form-input' type='' name='DisplayPicture[height]' placeholder='512' value="<?=(!empty($dp->height) ? $dp->height : '')?>" ></input>
                    <label class='form-label'>px</label>
                </div>
                <div>
                    <?=$dp->outputErrors('blocks')?>
                    <label class='form-label'>blocks</label>
                    <input class='form-input' type='' name='DisplayPicture[blocks]' placeholder='5' value="<?=(!empty($dp->blocks) ? $dp->blocks : '')?>" ></input>
                    <label class='form-label'>(example: 5 = 5x5)</label>
                </div>

                <button>generate</button>
            </form>
        </div>
    </div>
</div>


<script>
    document.querySelector('[name="DisplayPicture[width]"]').addEventListener('input', function(){
        document.querySelector('[name="DisplayPicture[height]"]').value = this.value;
    });
    document.querySelector('[name="DisplayPicture[height]"]').addEventListener('input', function(){
        document.querySelector('[name="DisplayPicture[width]"]').value = this.value;
    });
</script>
