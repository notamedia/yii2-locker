<?php

namespace notamedia\locker;

use yii\db\ActiveRecord;

/**
 * Lock record model
 *
 * @property string $hash - hash
 * @property string $locked_at - lock time
 * @property mixed $locked_by - lock author
 */
class Lock extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lock}}';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['hash'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hash', 'locked_at', 'locked_by'], 'required'],
            ['hash', 'string', 'max' => 64],
            ['locked_by', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hash'      => \Yii::t('notamedia/locker/labels', 'Hash'),
            'locked_at' => \Yii::t('notamedia/locker/labels', 'Lock date and time'),
            'locked_by' => \Yii::t('notamedia/locker/labels', 'Lock user identifier'),
        ];
    }
}