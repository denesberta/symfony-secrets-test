<?php

namespace App\Formatter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Interface for response formatters
 * Each implementation is responsible for serializing data into a specific format (e.g., JSON, XML)
 * and creating an appropriate Symfony Response object
 */
interface ResponseFormatterInterface
{
  /**
   * Checks if this formatter can handle any of the given MIME types
   *
   * @param string $format A list of MIME types from the Accept header
   * @return bool
   */
  public function supports(string $format): bool;
  
  /**
   * Creates a Response object with the data formatted correctly
   *
   * @param mixed $data The data to serialize
   * @param int $status The HTTP status code for the response
   * @throws ExceptionInterface
   * @return Response
   */
  public function format(mixed $data, int $status): Response;
}
