<?php

namespace App\Formatter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Formats the response as XML
 */
class XmlResponseFormatter implements ResponseFormatterInterface
{
  private const FORMAT = 'xml';
  
  private SerializerInterface $serializer;
  
  public function __construct(SerializerInterface $serializer)
  {
    $this->serializer = $serializer;
  }
  
  
  /**
   * @parentDoc
   */
  public function supports(string $format): bool
  {
    return $format === self::FORMAT;
  }
  
  /**
   * @parentDoc
   */
  public function format(mixed $data, int $status): Response
  {
    $serializedData = $this->serializer->serialize($data, self::FORMAT, [
      'groups' => 'secret:read',
      'xml_root_node_name' => 'secret'
    ]);
    return new Response($serializedData, $status, ['Content-Type' => 'application/xml']);
  }
}
