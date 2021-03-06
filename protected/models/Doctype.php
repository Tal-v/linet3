<?php
/***********************************************************************************
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * ("License"); You may not use this file except in compliance with the Mozilla Public License Version 2.0
 * The Original Code is:  Linet 3.0 Open Source
 * The Initial Developer of the Original Code is Adam Ben Hur.
 * All portions are Copyright (C) Adam Ben Hur.
 * All Rights Reserved.
 ************************************************************************************/
/**
 * This is the model class for table "docType".
 *
 * The followings are the available columns in table 'docType':
 * @property integer $id
 * @property string $name
 * @property integer $openformat
 * @property integer $isdoc
 * @property integer $isrecipet
 * @property integer $iscontract
 * @property integer $stockAction
 * @property integer $account_type
 * @property integer $docStatus_id
 * @property integer $last_docnum
 */
class Doctype extends basicRecord {

    
    
    
    const table = '{{docType}}';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Doctype the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

     public static function getList($const=null){
        //if($const===null){
            $arr= self::model()->findAll();
            
            //
        //}
        
        foreach($arr as &$item){
            $item->name=Yii::t('app',$item->name);
        }
        
        
        return CHtml::listData($arr, 'id', 'name');
    }
    
    
    public function getOpenType($key) {
        //$this->find
        $tmp = $this->findByAttributes(array('openformat' => $key));
        if ($tmp !== null)
            return Yii::t('app', $tmp->name);
        else {
            Yii::log("OpenFormat Import: no type:" . $key, 'error', 'app');
            //Yii::app()->end();
            return '';
        }
        // return isset($this->docType)?$this->docType->openformat:"";
    }

    public function primaryKey() {
        return 'id';
    }

    /**
     * @return string the associated database table name
     */
    public function delete() {
        if ($this->id >= 16) {//protect all sys docs
            return parent::delete();
        } else {
            return false;
        }
    }

    public function tableName() {
        return self::table;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, openformat, isdoc, isrecipet, iscontract, stockAction, account_type, docStatus_id, last_docnum', 'required'),
            array('openformat, isdoc, isrecipet, iscontract, stockAction, account_type, docStatus_id, last_docnum', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('header, footer', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, openformat, isdoc, isrecipet, iscontract, stockAction, account_type, docStatus_id, last_docnum', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.);

        return array(
            'docStatus' => array(self::BELONGS_TO, 'Docstatus', array('docStatus_id' => 'num', 'id' => 'doc_type')),
        );
    }

    public function getType($name) {
        $model = Doctype::model()->find('name=:name', array(':name' => ucfirst($name)));
        //$post=Post::model()->find('postID=:postID', array(':postID'=>10));
        return $model->id;
    }

    public function getOType($type) {
        $model = Doctype::model()->find('openformat=:openformat', array(':openformat' => $type));
        //$post=Post::model()->find('postID=:postID', array(':postID'=>10));
        if ($model === null)
            return 0;

        return $model->id;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('labels', 'ID'),
            'name' => Yii::t('labels', 'Name'),
            'openformat' => Yii::t('labels', 'Open Format'),
            'isdoc' => Yii::t('labels', 'Is Document'),
            'isrecipet' => Yii::t('labels', 'Is Recipet'),
            'iscontract' => Yii::t('labels', 'Is Contract'),
            'stockAction' => Yii::t('labels', 'Stock Action'),
            'account_type' => Yii::t('labels', 'Account Type'),
            'docStatus_id' => Yii::t('labels', 'Document Status'),
            'last_docnum' => Yii::t('labels', 'Last Document num'),
            'header' => Yii::t('labels', 'Header'),
            'footer' => Yii::t('labels', 'Footer'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('openformat', $this->openformat);
        $criteria->compare('isdoc', $this->isdoc);
        $criteria->compare('isrecipet', $this->isrecipet);
        $criteria->compare('iscontract', $this->iscontract);
        $criteria->compare('stockAction', $this->stockAction);
        $criteria->compare('account_type', $this->account_type);
        $criteria->compare('docStatus_id', $this->docStatus_id);
        $criteria->compare('last_docnum', $this->last_docnum);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
