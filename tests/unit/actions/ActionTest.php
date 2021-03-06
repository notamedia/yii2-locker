<?php

use tests\unit\Model;
use notamedia\locker\Lock;

class ActionTest extends \Codeception\Test\Unit
{
    /** @var UnitTester */
    protected $tester;

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        parent::_before();
        \Yii::$app->getUser()->loginByAccessToken(1);
    }

    /**
     * @inheritdoc
     */
    public function _fixtures()
    {
        return [
            'model' => [
                'class' => \tests\_data\fixtures\ModelFixture::class,
            ],
            'lock' => [
                'class' => \tests\_data\fixtures\LockFixture::class,
            ],
        ];
    }

    /**
     * Check record lock
     */
    public function testLockAction()
    {
        $model = Yii::createObject(Model::class);
        $model->setAttribute('id', 1);
        $model->save();

        $_POST['_method'] = 'LOCK';
        $request = Yii::$app->getRequest();
        $request->setBodyParams(['_method' => 'LOCK']);

        Yii::$app->runAction('test/lock', ['id' => 1]);

        $response = Yii::$app->getResponse();
        $this->assertEquals(204, $response->getStatusCode(), 'Correct response status');
        $this->assertEquals(1, Lock::find()->count(), 'Lock created');

        $lock = Lock::findOne(['hash' => $model->getLockHash()]);
        $this->assertEquals(1, $lock->locked_by, 'User set');
        $diffExpression = new \yii\db\Expression(
            'CAST(strftime([[locked_at]], CURRENT_TIMESTAMP) as integer)'
        );
        $diffSeconds = Lock::find()
            ->select($diffExpression)
            ->where(['hash' => $lock->getAttribute('hash')])
            ->scalar();
        $this->assertGreaterThanOrEqual(
            \Yii::$app->lockManager->lockTime['default'],
            $diffSeconds,
            'Lock time is correct'
        );
        $this->assertFalse(
            \Yii::$app->lockManager->checkLockActual($model, false),
            'For current user, lock inactive'
        );
        $this->assertFalse(
            \Yii::$app->lockManager->checkLockAuthor($lock, false),
            'For current user, lock inactive'
        );
        $this->assertGreaterThan(
            0,
            \Yii::$app->lockManager->checkLockTime($model, $lock, false),
            'For current time, lock active'
        );

    }

    /**
     * Check record unlock
     */
    public function testUnlockAction()
    {
        $model = Yii::createObject(Model::class);
        $model->setAttribute('id', 1);
        $model->save();

        $_POST['_method'] = 'UNLOCK';
        $request = Yii::$app->getRequest();
        $request->setBodyParams(['_method' => 'UNLOCK']);

        Yii::$app->runAction('test/lock', ['id' => 1]);

        $response = Yii::$app->getResponse();
        $this->assertEquals(204, $response->getStatusCode(), 'Correct response status');
        $this->assertEquals(1, Lock::find()->count(), 'Lock created');

        $lock = Lock::findOne(['hash' => $model->getLockHash()]);
        $this->assertEquals(1, $lock->locked_by, 'User set');
        $this->assertFalse(\Yii::$app->lockManager->checkLockActual($model), 'Lock not actual');
    }
}