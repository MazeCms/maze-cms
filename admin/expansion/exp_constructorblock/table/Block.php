<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin\expansion\exp_constructorblock\table;

use maze\table\Expansion;
use maze\db\Expression;
use maze\helpers\ArrayHelper;
use maze\table\ContentType;
use maze\table\InstallApp;
use Text;
use admin\expansion\exp_constructorblock\table\FilterBlock;
use admin\expansion\exp_constructorblock\table\ViewBlock;
use admin\expansion\exp_constructorblock\table\SortBlock;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Block extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%constructorblock_block}}';
    }
    public function rules() {
        return [
            [['title'], 'required'],
           
            ['code', 'required', 'on'=>'create'],
            [['expansion', 'bundle', 'list'], 'required'],
            ['code', 'match', 'pattern'=>'/^[a-z]{4,50}$/i', 'on'=>'create'],
            ['title', 'string', 'min'=>3, 'max'=>255],
            ['list', 'boolean'],
            ['multiple_size', 'validMultisize', 'skipOnEmpty'=>false],
            ['multiple_size', 'number', 'min'=>1],
            ['multiple_start', 'number', 'min'=>0],            
            ['code','unique', 'targetClass'=>'admin\expansion\exp_constructorblock\table\Block', 'targetAttribute'=>'code', 'on'=>'create'],
            [['description'], 'safe']
        ];
    }
    
    public function validMultisize(){
       
        if($this->list){
            if(empty($this->multiple_size) || $this->multiple_size < 1){
                $this->addError('multiple_size', Text::_("Количество записей в блоке обязательное поле"));
            }
        }
    }
    
    public function getType(){
        return $this->hasOne(ContentType::className(), [
            'bundle' => 'bundle', 
            'expansion' => 'expansion'
            ])->from(["type"=>ContentType::tableName()]);
    }
    
    public function getFilter()
    {
        return $this->hasMany(FilterBlock::className(), ['code' => 'code'])->from(['f'=>FilterBlock::tableName()]);
    }
    
    public function getSort()
    {
        return $this->hasMany(SortBlock::className(), ['code' => 'code'])->from(['s'=>SortBlock::tableName()]);
    }
    
    public function getView()
    {
        return $this->hasMany(ViewBlock::className(), ['code' => 'code'])->from(['v'=>ViewBlock::tableName()]);
    }
    
    public function getInstallApp() {
        return $this->hasOne(InstallApp::className(), ['name' => 'expansion'])
                        ->from(["ia" => InstallApp::tableName()])
                        ->andOnCondition(['ia.type'=>'expansion']);
    }
    
    public static function getList($condition = [])
    {
       return ArrayHelper::map(static::find()->andFilterWhere($condition)->asArray()->all(), 'code', 'title'); 
    }

    public function beforeSave($insert) {
        
        if($this->isNewRecord){             
            $this->date_create = new Expression('NOW()');
        }
        
        return true;
    }

    public function attributeLabels() {
        return[
            'code'=>Text::_('EXP_CONSTRUCTORBLOCK_BLOCK_CODE_LABEL'),
            'bundle'=>Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_BUNDLE_LABEL"),
            'expansion'=>Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_EXPANSION_LABEL"),
            'title'=>Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_TITLE_LABEL"),
            'description'=>Text::_("EXP_CONSTRUCTORBLOCK_BLOCK_DESCRIPTION_LABEL"),
            'list'=>Text::_("EXP_CONSTRUCTORBLOCK_LIST_LABEL"),
            'multiple_size'=>Text::_("EXP_CONSTRUCTORBLOCK_MULTIPLE_SIZE_LABEL"),
            'multiple_start'=>Text::_("EXP_CONSTRUCTORBLOCK_MULTIPLE_START_LABEL"),
        ];
    }

}
