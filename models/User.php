<?php
namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

class User extends BaseObject implements IdentityInterface {
  public $id = '';
  public string $username = '';
  public string $passwordHash = '';
  public string $authKey = '';
  public string $accessToken = '';
  private static ?array $_users = null;

  private static function getUsers(): array
  {
    if (self::$_users !== null) {
      return self::$_users;
    }

    $app = Yii::$app;
    if ($app === null) {
      self::$_users = [];
      return self::$_users;
    }

    $users = $app->params['usuarios'] ?? [];
    self::$_users = is_array($users) ? $users : [];

    return self::$_users;
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentity($id) {
    $users = self::getUsers();
    foreach ($users as $user) {
      if (($user['id'] ?? null) === (string)$id) {
        return new static($user);
      }
    }
    return null;
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentityByAccessToken($token, $type = null) {
    $users = self::getUsers();
    foreach ($users as $user) {
      if ($user['accessToken'] === $token) {
        return new static($user);
      }
    }

    return null;
  }

  /**
   * Finds user by username
   *
   * @param string $username
   * @return static|null
   */
  public static function findByUsername(string $username) {
    $users = self::getUsers();
    foreach ($users as $user) {
      if (strcasecmp($user['username'], $username) === 0) {
        return new static($user);
      }
    }

    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthKey() {
    return $this->authKey;
  }

  /**
   * {@inheritdoc}
   */
  public function validateAuthKey($authKey): bool {
    return $this->authKey === $authKey;
  }
}
