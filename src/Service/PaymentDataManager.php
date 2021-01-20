<?php

namespace App\Service;

use App\Entity\User;
use App\UnexpectedStatusCodeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentDataManager
{
    private const ENDPOINT = "default/wunderfleet-recruiting-backend-dev-save-payment-data";

    private $paymentApiClient;

    private $entityManager;

    public function __construct(HttpClientInterface $paymentApiClient, EntityManagerInterface $entityManager)
    {
        $this->paymentApiClient = $paymentApiClient;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @throws UnexpectedStatusCodeException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function savePaymentData(User $user)
    {
        $body = [
            'customerId' => $user->getId(),
            'iban' => $user->getPaymentDetails()->getIban(),
            'owner' => $user->getPaymentDetails()->getAccountOwner()
        ];

        $response = $this->paymentApiClient->request('POST', self::ENDPOINT, [
            'body' => json_encode($body)
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedStatusCodeException(
                "Payment Api has returned unexpected status code:" .
                $response->getStatusCode()
            );
        }

        $paymentDataId = $response->toArray()['paymentDataId'];

        $paymentDetails = $user->getPaymentDetails();
        $paymentDetails->setPaymentDataId($paymentDataId);

        $this->entityManager->persist($paymentDetails);
        $this->entityManager->flush();
    }
}