<?php

namespace App\Service;

use Traversable;
use App\Formatter\ResponseFormatterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A factory that selects the correct response formatter based on the request's Accept header
 * It iterates over all services that implement ResponseFormatterInterface
 */
class ResponseFormatterFactory
{
  /** @var ResponseFormatterInterface[] */
  private iterable $formatters;
  private ResponseFormatterInterface $defaultFormatter;
  
  /**
   * @param Traversable $formatters A collection of services tagged with 'app.response_formatter'
   * @param ResponseFormatterInterface $defaultFormatter The formatter to use as a fallback
   */
  public function __construct(Traversable $formatters, ResponseFormatterInterface $defaultFormatter)
  {
    $this->formatters = $formatters;
    $this->defaultFormatter = $defaultFormatter;
  }
  
  /**
   * Selects the appropriate formatter based on the Accept header
   *
   * @param Request $request
   * @return ResponseFormatterInterface
   */
  public function create(Request $request): ResponseFormatterInterface
  {
    $format = $request->getPreferredFormat();
    
    foreach ($this->formatters as $formatter) {
      if ($formatter->supports($format)) {
        return $formatter;
      }
    }
    
    // If no specific formatter matches the preferred format, use the default
    return $this->defaultFormatter;
  }
}
