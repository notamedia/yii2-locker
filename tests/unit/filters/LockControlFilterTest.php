<?php

use tests\unit\Model;
use notamedia\locker\Lock;

class LockControlFilterTest extends \Codeception\Test\Unit
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
     * Check try update current user lock
     */
    public function testUpdateWithOwnActualLockAction()
    {
        $model = Yii::createObject(Model::class);
        $model->setAttribute('id', 1);
        $model->save();
        \Yii::$app->lockManager->activateLock($model);

        $_POST['_method'] = 'POST';
        $request = Yii::$app->getRequest();
        $request->setBodyParams(['_method' => 'POST', 'id' => 1]);

        Yii::$app->runAction('test/update', ['id' => 1]);

        $response = Yii::$app->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Correct response status');
    }

    /**
     * Check try update another user lock
     * @expectedException \notamedia\locker\LockAnotherUserException
     * @expectedExceptionMessage Resource is blocked by the another user
     */
    public function testUpdateWithForeignActualLockAction()
    {
        $model = Yii::createObject(Model::class);
        $model->setAttribute('id', 1);
        $model->save();

        $lock = Yii::createObject(Lock::class);
        $lock->setAttributes([
            'hash'      => $model->getLockHash(),
            'locked_by' => 9,
            'locked_at' => (new \DateTime('yesterday'))->format('Y-m-d H:i:s')
        ]);
        $lock->save();

        $_POST['_method'] = 'POST';
        $request = Yii::$app->getRequest();
        $request->setBodyParams(['_method' => 'POST', 'id' => 1]);

        Yii::$app->runAction('test/update', ['id' => 1]);
    }
}