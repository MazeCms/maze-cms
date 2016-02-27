<?php

namespace exp\exp_install\model;

use maze\base\Model;
use RC;
use Text;
use maze\db\Connection;

class AccountInstall extends Model {

    public $username;
    public $password;
    public $repeat_password;
    public $email;     
    public $timezone = 'Europe/Moscow';
    public $site_name;
    public $fromname;
    public $mailfrom;
    public $language = 'ru-RU';



    public function rules(){
        return [
            [['username', 'password', 'email', 'timezone', 'site_name', 'mailfrom', 'fromname', 'language'], 'required', "message"=>"Поле ({attribute}) является обязательным"],
            [['email', 'mailfrom'],'email', 'message'=>'E-mail является недествительным'],
            ['password', 'match', 'pattern'=>'/^[a-z0-9-_.\#]{6,80}$/i', 
                'message'=>Text::_('Пароль {pass} является недествительным, используйте только латински буквы и цифры от 6 до 80', ['pass'=>$this->password])],
            ['repeat_password', 'compare', 'compareAttribute'=>'password',  'message'=>'Пароли не совпадают' ],            
            ['username', 'match', 'pattern'=>'/^[a-z]{4,80}$/i', 'message'=>'Имя пользователя является недопустимым'],  
        ];
    }
    
   
    
    public function attributeLabels() {
        return[
            "username"=>"Логин администратора",
            "password" => "Пароль",
            "repeat_password" => "Повторите пароль",
            "email" => "Электронная почта",
            "timezone"=>"Часовой пояс", 
            "site_name"=>"Название сайта",
            "fromname"=>"Имя отправителя",
            "mailfrom"=>"E-mail сайта",
            "language"=>"Локаль"
        ];
    }

    

}
