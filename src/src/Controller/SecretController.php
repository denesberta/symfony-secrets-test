<?php

namespace App\Controller;

use App\Dto\CreateSecretRequest;
use App\Service\ResponseFormatterFactory;
use App\Service\SecretService;
use App\Service\ValidationErrorFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/v1', name: 'api_v1_')]
class SecretController extends AbstractController
{
  private SecretService $secretService;
  private ResponseFormatterFactory $formatterFactory;
  private ValidatorInterface $validator;
  private ValidationErrorFormatter $errorFormatter;

  public function __construct(
    SecretService $secretService,
    ResponseFormatterFactory $formatterFactory,
    ValidatorInterface $validator,
    ValidationErrorFormatter $errorFormatter,
  ) {
    $this->secretService = $secretService;
    $this->formatterFactory = $formatterFactory;
    $this->validator = $validator;
    $this->errorFormatter = $errorFormatter;
  }

  /**
   * Creates a new secret from POST data after validation
   * The response format is determined by the 'Accept' header
   * TODO: Add rate limiting
   * @throws ExceptionInterface
   */
  #[Route('/secret', name: 'secret_add', methods: ['POST'])]
  public function addSecret(Request $request): Response
  {
    $formatter = $this->formatterFactory->create($request);

    $createSecretRequest = new CreateSecretRequest();
    $createSecretRequest->secret = $request->request->get('secret');
    $createSecretRequest->expireAfterViews = $request->request->get('expireAfterViews') !== null
      ? (int)$request->request->get('expireAfterViews')
      : null;
    $createSecretRequest->expireAfter = $request->request->get('expireAfter') !== null
      ? (int)$request->request->get('expireAfter')
      : null;

    $errors = $this->validator->validate($createSecretRequest);

    if (count($errors) > 0) {
      return $formatter->format(
        ['errors' => $this->errorFormatter->format($errors)],
        Response::HTTP_BAD_REQUEST
      );
    }

    try {
      $secret = $this->secretService->createSecret(
        $createSecretRequest->secret,
        $createSecretRequest->expireAfterViews,
        $createSecretRequest->expireAfter
      );

      return $formatter->format($secret, Response::HTTP_OK);
    } catch (\Exception $e) {
      return $formatter->format(
        ['error' => 'An unexpected server error occurred.Error: ' . $e],
        Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  /**
   * Retrieves a secret by its hash
   * The response format (JSON or XML) is determined by the 'Accept' header
   * @throws ExceptionInterface
   */
  #[Route('/secret/{hash}', name: 'secret_get', methods: ['GET'])]
  public function getSecret(string $hash, Request $request): Response
  {
    $secret = $this->secretService->getValidSecretByHash($hash);
    $formatter = $this->formatterFactory->create($request);

    if (!$secret) {
      return $formatter->format(
        ['error' => 'Secret not found.'],
        Response::HTTP_NOT_FOUND
      );
    }

    return $formatter->format(
      $secret,
      Response::HTTP_OK
    );
  }
}
