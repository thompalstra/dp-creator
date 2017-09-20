<?php
namespace common\models;

use common\components\CoreValidator;

class Model{
    public static function className(){
        return get_called_class();
    }
    public function addError($attribute, $message){
        $this->_errors[$attribute][] = $message;
    }
    public function hasErrors(){
        return (empty($this->_errors));
    }
    public function validate(){
        foreach($this->rules() as $rule){
            $validator = $rule[1];
            $attributes = $rule[0];
            foreach($attributes as $attribute){
                if(method_exists(CoreValidator::className(), $validator)){
                    CoreValidator::$validator($this, $attribute, $rule);
                } else if(method_exists($this::className(), $validator)) {
                    $this->$validator($this, $attribute, $rule);
                }

            }
        }
        return empty($this->_errors);
    }
    public function load($params){
        $class = get_called_class();
        $explode = explode('\\', $class);
        $class = $explode[count($explode) - 1];
        if(isset($params[$class])){
            foreach($params[$class] as $key => $value){
                $this->$key = $value;
            }
            return true;
        } else {
            return false;
        }
    }

    public function outputErrors($attribute){
        $out = "";
        if(!empty($this->_errors[$attribute])){
            $out .= "<label>";
            foreach($this->_errors[$attribute] as $message){
                $out .= "<p>$message</p>";
            }
            $out .= "</label>";
        }
        return $out;
    }

}

?>
