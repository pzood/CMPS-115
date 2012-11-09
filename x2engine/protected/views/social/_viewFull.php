<?php
/*********************************************************************************
 * The X2CRM by X2Engine Inc. is free software. It is released under the terms of 
 * the following BSD License.
 * http://www.opensource.org/licenses/BSD-3-Clause
 * 
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95067 USA
 * 
 * Company website: http://www.x2engine.com 
 * Community and support website: http://www.x2community.com 
 * 
 * Copyright (C) 2011-2012 by X2Engine Inc. www.X2Engine.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this 
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this 
 *   list of conditions and the following disclaimer in the documentation and/or 
 *   other materials provided with the distribution.
 * - Neither the name of X2Engine or X2CRM nor the names of its contributors may be 
 *   used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************/

Yii::app()->clientScript->registerScript('feedLinkJs', "
$(document).ready(function() {
	$('.view.top-level').children().bind('click',function(e) {
		e.stopPropagation();
	});
});
",CClientScript::POS_HEAD);

$authorRecord = CActiveRecord::model('User')->findByAttributes(array('username'=>$data->user));
$author = $authorRecord->name; //firstName.' '.$authorRecord->lastName;
$commentDataProvider=new CActiveDataProvider('Social', array(
	'criteria'=>array(
		'order'=>'timestamp ASC',
		'condition'=>"type='comment' AND associationId=$data->id",
)));
?>
<div class="view top-level">
	<div class="deleteButton">
		<?php
		if($data->user==Yii::app()->user->getName() || $data->associationId==Yii::app()->user->getId())
			echo CHtml::link('[x]',array('deletePost','id'=>$data->id,'redirect'=>Yii::app()->controller->action->id)); //,array('class'=>'x2-button') ?>
	</div>
	<?php echo CHtml::link(Yii::t('profile','Reply'),'#',array('onclick'=>"$('#addReply-".$data->id."').toggle();",'class'=>'x2-button float')); ?>

	<?php
	if($authorRecord->id != $data->associationId && $data->associationId != 0) {
		$temp=Profile::model()->findByPk($data->associationId);
		$recipient=$temp->fullName;
		$modifier=' &raquo; ';
	} else {
		$recipient='';
		$modifier='';
	}
	?>
	<?php echo CHtml::link($author,array('profile/view','id'=>$authorRecord->id)).$modifier.CHtml::link($recipient,$data->associationId); ?> <span class="comment-age"><?php echo x2base::timestampAge(date("Y-m-d H:i:s",$data->timestamp)); ?></span><br />
	<?php echo MediaChild::attachmentSocialText($data->data,true,true); ?><br />
	<?php 
	if(count($commentDataProvider->getData())>0){
		$this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$commentDataProvider,
		'itemView'=>'../social/_view',
		'template'=>'{items}'
	));
	}
	
	echo CHtml::beginForm(
		'addComment',
		'get',
		array(
			'style'=>'display:none;',
			'id'=>'addReply-'.$data->id,
		));
	echo CHtml::textArea('comment','',array('style'=>'heght:40px; width:440px;display:block;clear:both;'));
	echo CHtml::hiddenField('id',$data->id);
	echo CHtml::hiddenField('redirect',Yii::app()->controller->action->id);
	echo CHtml::submitButton(Yii::t('app','Submit'),array('class'=>'x2-button float'));
	echo CHtml::endForm();
	
	
	?>
</div>
<?php


/*
<div class="view">
	<div class="deleteButton">
		<?php echo CHtml::link('[x]',array('deleteNote','id'=>$data->id)); //,array('class'=>'x2-button') ?>
		<?php //echo CHtml::link("<img src='".Yii::app()->request->baseUrl."/images/deleteButton.png' />",array("deleteNote","id"=>$data->id)); ?>
	</div>

	<b><?php echo CHtml::encode($data->getAttributeLabel('createdBy')); ?>:</b>
	<?php echo CHtml::encode($data->createdBy); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('createDate')); ?>:</b>
	<?php echo CHtml::encode($data->createDate); ?>
	<br /><br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('note')); ?>:</b>
	<?php echo CHtml::encode($data->note); ?>
	<br />
</div>
*/
?>