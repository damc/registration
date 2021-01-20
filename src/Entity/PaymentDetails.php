<?php

namespace App\Entity;

use App\Repository\PaymentDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentDetailsRepository::class)
 */
class PaymentDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accountOwner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentDataId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountOwner(): ?string
    {
        return $this->accountOwner;
    }

    public function setAccountOwner(string $accountOwner): self
    {
        $this->accountOwner = $accountOwner;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getPaymentDataId(): ?string
    {
        return $this->paymentDataId;
    }

    public function setPaymentDataId(?string $paymentDataId): self
    {
        $this->paymentDataId = $paymentDataId;

        return $this;
    }
}
