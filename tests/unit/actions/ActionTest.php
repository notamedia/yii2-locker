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

        /** @var \yii\db\Migration $migration */
        $migration = \Yii::createObject(\notamedia\locker\migrations\m000000_000000_create_table_lock::class);
        $migration->up();

        $this->tester->haveFixtures([
            'model' => [
                'class' => \tests\_data\fixtures\ModelFixture::class,
            ],
            'lock' => [
                'class' => \tests\_data\fixtures\LockFixture::class,
            ],
        ]);

        \Yii::$app->getUser()->loginByAccessToken(1);
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

    /**
     * Check record unlock with another configuration
     * @expectedException \notamedia\locker\LockAnotherUserException
     * @expectedExceptionMessage Resource is blocked by the another user
     */
    public function testUnlockWithAnotherConfigurationAction()
    {
        $oldConfig = \Yii::$container->getDefinitions();
        \Yii::$container->set(
            \notamedia\locker\LockInterface::class,
            function($container, $params, $config){
                list($user, $resource) = $params;
                $lock = Lock::findOrCreate($user, $resource);
                $lock->locked_by = 19;

                return $lock;
            }
        );

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
        $this->assertEquals('0000-00-00 00:00:00', $lock->secret, 'Another lock behavior');
        $this->assertFalse(\Yii::$app->lockManager->checkLockActual($model), 'Lock not actual');

        \Yii::$container->setDefinitions($oldConfig);
    }
}