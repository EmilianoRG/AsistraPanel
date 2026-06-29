<?php
namespace app\models;

use yii\base\Model;

class AppModel extends Model {
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
}