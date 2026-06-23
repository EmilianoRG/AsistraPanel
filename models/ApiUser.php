<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_user".
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $role
 * @property string|null $auth_key
 * @property string $created_at
 * @property string|null $updated_at
 */
class ApiUser extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_key', 'updated_at'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 'admin'],
            [['username', 'password_hash'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 100],
            [['password_hash'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 64],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'role' => 'Role',
            'auth_key' => 'Auth Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
