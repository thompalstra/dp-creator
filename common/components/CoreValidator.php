<?php
namespace common\components;

class CoreValidator{


    public static function className(){
        return get_called_class();
    }

    public static function number($model, $attribute, $rule){
        if($model->$attribute === null && isset($rule['default'])){
            $model->$attribute = $rule['default'];
        }
        if(!is_numeric($model->$attribute)){
            $model->addError($attribute, "$attribute is not a number");
        } else {
            if(isset($rule['max'])){
                $max = $rule['max'];
                if($model->$attribute > $max){
                    $model->addError($attribute, "$attribute cannot be less than $max");
                }
            }
            if(isset($rule['min'])){
                $min = $rule['min'];
                if($model->$attribute < $min){
                    $model->addError($attribute, "$attribute cannot be less than $min");
                }
            }
        }
    }
}

?>
