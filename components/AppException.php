<?php
namespace app\components;

class AppException extends \Exception {
  public string $errorMessage;
  public array $errorDetail;
  public array $errorMessages;
  public int $httpCode;

  public function __construct(string $errorMessage = '', int $httpCode = 500) {
    parent::__construct();
    $this->errorMessage = $errorMessage;
    $this->httpCode = $httpCode;
    $this->errorMessages = [];
    $this->errorDetail = [];
  }

  public function hasErrorMessages(): bool {
    return $this->errorMessages && count($this->errorMessages) > 0;
  }

  public function hasErrorDetail(): bool {
    return $this->errorDetail && count($this->errorDetail) > 0;
  }

  public function toArray(): array {
    $response = [];
    if ($this->errorMessage) {
      $response['errorMessage'] = $this->errorMessage;
    }
    if ($this->hasErrorMessages()) {
      $response['errorMessages'] = $this->errorMessages;
    }
    if ($this->hasErrorDetail()) {
      $response['errorDetail'] = $this->errorDetail;
    }
    return $response;
  }
}