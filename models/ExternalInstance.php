<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "external_instances".
 *
 * @property int $id
 * @property string $name
 * @property string $base_url
 * @property string|null $api_key
 * @property int|null $status
 * @property string|null $last_sync_at
 * @property string $created_at
 * @property string|null $updated_at
 */
class ExternalInstance extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_instances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['api_key', 'last_sync_at', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['name', 'base_url'], 'required'],
            [['status'], 'integer'],
            [['last_sync_at', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 150],
            [['base_url', 'api_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'base_url' => 'Base Url',
            'api_key' => 'Api Key',
            'status' => 'Status',
            'last_sync_at' => 'Last Sync At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
