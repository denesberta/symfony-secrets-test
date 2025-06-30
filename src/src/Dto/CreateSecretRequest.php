<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class holds the validation constraints for the incoming data
 */
class CreateSecretRequest
{
  #[Assert\NotBlank(message: "The secret cannot be empty.")]
  public ?string $secret = null;
  
  #[Assert\NotBlank(message: "The expireAfterViews field is required.")]
  #[Assert\Type(type: "integer", message: "The value {{ value }} is not a valid integer.")]
  #[Assert\GreaterThan(value: 0, message: "The secret must be available for at least 1 view.")]
  public ?int $expireAfterViews = null;
  
  #[Assert\NotBlank(message: "The expireAfter field is required.")]
  #[Assert\Type(type: "integer", message: "The value {{ value }} is not a valid integer.")]
  #[Assert\GreaterThanOrEqual(value: 0, message: "The expiration time cannot be negative.")]
  public ?int $expireAfter = null;
}
