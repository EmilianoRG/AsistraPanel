<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 */
class LoginForm extends Model {
  public string $username = '';
  public string $password = '';
  public bool $rememberMe = true;
  private $_user = null;
  private bool $_userLoaded = false;

  public function __construct(Security $security, $config = []) {
    parent::__construct($config);
  }

  /**
   * @return array the validation rules.
   */
  public function rules(): array {
    return [
      [['username', 'password'], 'required'],
      ['rememberMe', 'boolean'],
      ['password', 'validatePassword'],
    ];
  }

  /**
   * Validates the password.
   * This method serves as the inline validation for password.
   *
   * @param string $attribute the attribute currently being validated
   * @param array $params the additional name-value pairs given in the rule
   */
  public function validatePassword(string $attribute, $params): void {
    if (!$this->hasErrors()) {
      $user = $this->getUser();

      if (!$user || !\Yii::$app->security->validatePassword($this->password, $user->passwordHash)) {
        $this->addError($attribute, 'Usuario o contraseña incorrectos');
      }
    }
  }

  /**
   * Logs in a user using the provided username and password.
   * @return bool whether the user is logged in successfully
   */
  public function login(): bool {
    if ($this->validate()) {
      return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    return false;
  }

  /**
   * Finds user by [[username]]
   *
   * @return User|null
   */
  public function getUser() {
    if (!$this->_userLoaded) {
      $this->_user = User::findByUsername($this->username);
      $this->_userLoaded = true;
    }

    return $this->_user;
  }
}