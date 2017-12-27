<?php

namespace notamedia\locker;

use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Lock record model
 *
 * @property string $hash - hash
 * @property string $locked_at - lock time
 * @property mixed $locked_by - lock author
 */
class Lock extends ActiveRecord implements LockInterface
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

    /**
     * @inheritdoc
     */
    public static function findOrCreate(IdentityInterface $user, LockableInterface $resource)
    {
        $hash = $resource->getLockHash();
        $lock = self::findOne(['hash' => $hash]);
        if(!$lock instanceof self) {
            /** @var LockInterface $lock */
            $lock = \Yii::createObject(self::class);
            $lock->setAttributes([
                'hash'      => $hash,
                'locked_at' => new Expression('NOW()'),
                'locked_by' => $user->getId()
            ]);
        }

        return $lock;
    }

    /**
     * @inheritdoc
     */
    public function activate(IdentityInterface $user, Expression $lockedAtExpression)
    {
        $this->setAttributes([
            'locked_by' => $user->getId(),
            'locked_at' => $lockedAtExpression
        ]);
    }

    /**
     * @inheritdoc
     */
    public function deactivate()
    {
        $this->setAttributes([
            'locked_at' => new Expression('NOW()')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getLockTimeLeft(Expression $diffExpression)
    {
        return self::find()
            ->select($diffExpression)
            ->where(['hash' => $this->getAttribute('hash')])
            ->scalar();
    }
}