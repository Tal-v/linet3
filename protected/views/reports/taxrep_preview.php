<?php 

$this->beginWidget('MiniForm',array(
    'haeder' => Yii::t('app',"Tax Report"),
)); 

$form=$this->beginWidget('CActiveForm', array(
    'id'=>'reporttaxrep-form',
    //'enableAjaxValidation'=>true,
    
    'enableAjaxValidation'=>false,
        'htmlOptions'=>array(
                               'onsubmit'=>"return false;",/* Disable normal form submit */
                               'onkeypress'=>" if(event.keyCode == 13){ send(); } " /* Do ajax call when user presses enter key */
            ),
)); 



echo Yii::t('app','From Date');

echo $form->hiddenField($model,'step');
echo $form->hiddenField($model,'from_month');
echo $form->hiddenField($model,'year');              

echo "<br>";

echo Yii::t('app','To Date');
echo $form->hiddenField($model,'to_month');
//echo $form->hiddenField($model,'to_year'); 
echo $form->hiddenField($model,'selvat_acc');  


echo $form->labelEx($model,'selvat_total'); 
echo $form->textField($model,'selvat_total');

echo $form->labelEx($model,'income_sum'); 
echo $form->textField($model,'income_sum');

echo $form->labelEx($model,'income_sum_novat'); 
echo $form->textField($model,'income_sum_novat');
echo '<br />';



echo $form->hiddenField($model,'assetvat_acc');  

echo $form->labelEx($model,'assetvat_total'); 
echo $form->textField($model,'assetvat_total');

echo $form->hiddenField($model,'buyvat_acc');  

echo $form->labelEx($model,'buyvat_total'); 
echo $form->textField($model,'buyvat_total');

echo $form->labelEx($model,'payvat_total'); 
echo $form->textField($model,'payvat_total');

echo CHtml::submitButton('Pay',array('onclick'=>'send();')); 

 $this->endWidget(); 

$this->endWidget(); 
?>

<script type="text/javascript">
 
function send()
 {
 
   var data=$("#reporttaxrep-form").serialize();
 
 
  $.ajax({
   type: 'POST',
    url: '<?php echo Yii::app()->createAbsoluteUrl("reports/taxrep"); ?>',
   data:data,
success:function(data){
                $('#content > div').html(data);
              },
   error: function(data) { // if error occured
         //alert("Error occured.please try again");
         alert(data);
    },
 
  dataType:'html'
  });
 
}
 
</script>