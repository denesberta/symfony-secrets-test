<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * A utility service to format validation errors from the Validator component
 */
class ValidationErrorFormatter
{
  /**
   * Converts a ConstraintViolationList into a readable array
   *
   * @param ConstraintViolationListInterface $violations The list of constraint violations
   * @return array A structured array of error messages
   */
  public function format(ConstraintViolationListInterface $violations): array
  {
    $errors = [];
    foreach ($violations as $violation) {
      $errors[$violation->getPropertyPath()][] = $violation->getMessage();
    }
    return $errors;
  }
}
