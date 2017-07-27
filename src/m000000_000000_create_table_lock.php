<?php

namespace notamedia\locker;

/**
 * Migration for create needed table.
 *
 * ```php
 *  'controllerMap' => [
 *      //...
 *      'migrate' => [
 *          'class' => \yii\console\controllers\MigrateController::class,
 *          'migrationNamespaces' => [
 *              'notamedia\locker',
 *          ],
 *          //...
 *      ],
 *      //...
 *  ]
 * ```
 */
class m000000_000000_create_table_lock extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lock}}', [
            'hash' => $this->string(64)->unique()->notNull(),
            'locked_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'locked_by' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[hash]])',
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%lock}}');
    }
}
