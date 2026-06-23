<?php
namespace app\models;

use yii\db\ActiveRecord;

class AppActiveRecord extends ActiveRecord {
  public array $errorMessages = [];

  public function hasErrorMessages(): bool {
    return count($this->errorMessages) > 0;
  }

  public function getModelErrors(array $params = []): array {
    $includeAttributes = $params['includeAttributes'] ?? false;
    $separator = $params['separator'] ?? ': ';
    $errorList = [];
    foreach ($this->getErrors() as $attribute => $errors) {
      foreach ($errors as $error) {
        $errorList[] = $includeAttributes ? "**{$attribute}**{$separator}{$error}" : $error;
      }
    }
    return $errorList;
  }

  public function getFullErrors(array $params = []): array {
    return array_merge($this->errorMessages, $this->getModelErrors($params));
  }

  // especial, mas que nada para rest api
  public function getErrorDetail(): array {
    $errorDetail = [];
    foreach ($this->getErrors() as $attribute => $errors) {
      $errorDetail[$attribute] = [];
      foreach ($errors as $error) {
        $errorDetail[$attribute][] = $error;
      }
    }
    return $errorDetail;
  }
}