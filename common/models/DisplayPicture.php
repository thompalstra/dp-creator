<?php
namespace common\models;

use common\models\Model;

class DisplayPicture extends Model{
    public $completed = false;
    public $_errors = [];
    static $colorList = [
        [
            'r' => 231,
            'g' => 89,
            'b' => 38
        ],
        [
            'r' => 35,
            'g' => 182,
            'b' => 132,
        ]
    ];
    static $supportedSizes = [
        128,
        256,
        512,
        1024,
        2048
    ];

    public function rules(){
        return [
            [['width', 'height', 'padding'], 'number'],
            [['blocks'], 'number', 'min' => 1, 'max' => 128],
            [['width', 'height'], 'heightValidator'],
        ];
    }

    public function heightValidator($model, $attribute, $rule){
        foreach($model::$supportedSizes as $size){
            if($model->$attribute == $size){
                return true;
            }
        }
        $sizes = implode(', ', $model::$supportedSizes);
        $model->addError($attribute, "$attribute size not supported. Only $sizes are supported.");
    }

    public function generate(){
        if(!isset($_SESSION['id'])){
            $_SESSION['id'] = uniqid();
        }
        $padding = $this->padding;

        $m = max($this->width, $this->height);

        $width = $m;
        $height = $m;

        $b = $this->blocks;

        $desiredBlocksY = $desiredBlocksX = $b;

        $offsetX = $padding;
        $offsetY = $padding;

        $offsetWidth = $width - ($offsetX * 2);
        $offsetHeight = $height - ($offsetY * 2);


        $img = imagecreatetruecolor($width, $height);
        $fill = imagecolorallocate ( $img, 221, 221, 221 );

        imagefilledrectangle(
            $img,
            0,
            0,
            $width,
            $height,
            $fill
        );



        $maxRow = $desiredBlocksX - 1;
        $currentRow = 0;
        $maxRect = ceil($desiredBlocksY / 2) - 1;
        $currentRect = 0;
        $maxCount = ($maxRow + 1) * ($maxRect + 1);
        $currentCount = 0;
        $bCount = 0;

        $rectH = $offsetHeight / $desiredBlocksY;
        $rectW = $offsetWidth / $desiredBlocksX;


        $randomColor = self::$colorList[rand(0, count(self::$colorList) -1)];

        $base = [
            'r' => $randomColor['r'],
            'g' => $randomColor['g'],
            'b' => $randomColor['b']
        ];

        $hasCol = false;

        while($currentCount < $maxCount){

            if(!isset($arr[$currentRow])){
                $arr[$currentRow] = [];
            }

            $r = 0;
            $g = 0;
            $b = 0;

            if(rand(rand(0, 10), rand(0, 10) * 2) % 2 == 0){
                $r = $base['r'];
                $g = $base['g'];
                $b = $base['b'];
                $hasCol = true;
            }

            $arr[$currentRow][$currentRect] = [
                'x' => $offsetX + ($rectW * $currentRect),
                'y' => $offsetY + ($rectH * $currentRow),
                'x2' => $offsetX + (($rectW * $currentRect) + $rectW),
                'y2' => $offsetY + (($rectH * $currentRow) + $rectH),
                'col' => [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                ]
            ];

            if($currentRect < $maxRect){
                $currentRect++;
            } else {
                $currentRect = 0;
                $currentRow++;
            }

            $currentCount++;
        }

        if(!$hasCol){
            $this->completed = false;
        }

        $currentRect = 0;
        $currentRow = 0;



        $lStart = 0;
        // value = 2 when uneven due to offset
        $even = ($desiredBlocksY % 2 == 0);
        $lEnd = ceil( $desiredBlocksY / 2 ) - 1;
        foreach($arr as $rowKey => $row){
            foreach($row as $rectKey => $rect){
                if($rectKey >= $lStart && $rectKey <= $lEnd){
                    $newRectKey = ($desiredBlocksY - 1 ) - $rectKey;

                    $rect['x'] = $offsetX + ($rectH * $newRectKey);
                    $rect['y'] = $offsetY + ($rectW * $rowKey);
                    $rect['x2'] = $offsetX + (($rectW * $newRectKey) + $rectW);
                    $rect['y2'] = $offsetY + (($rectH * $rowKey) + $rectH);
                    $arr[$rowKey][$newRectKey] = $rect;
                }
            }
        }
        foreach($arr as $row){
            foreach($row as $rect){


                if($rect['col']['r'] == 0 && $rect['col']['g'] == 0 && $rect['col']['b'] == 0){

                } else {
                    $color = imagecolorallocate ( $img, $rect['col']['r'], $rect['col']['g'], $rect['col']['b'] );
                    imagefilledrectangle(
                        $img,
                        $rect['x'],
                        $rect['y'],
                        $rect['x2'],
                        $rect['y2'],
                        $color
                    );
                }

            }
        }

        $sessionId = $_SESSION['id'];

        if(!is_dir('generated')){
            mkdir('generated', 0777, true);
        }

        //if($this->completed){
            // header("Content-Type: image/jpg");
            imagejpeg($img,"generated/$sessionId.jpg",100);
            // readfile('myimg.jpg');
            return "$sessionId.jpg";
            exit();
        //} else {
        //    return $this->generate();
        //}


    }
}
?>
