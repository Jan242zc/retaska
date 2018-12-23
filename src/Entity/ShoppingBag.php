<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShoppingBagRepository")
 */
class ShoppingBag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $productId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $productName;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $productPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $productAmount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objednavka", inversedBy="ShoppingBag")
     */
    private $objednavka;
    
    public function __construct($productId, $productName, $productAmount, $productPrice)
    {
        $this->setProductId($productId);
        $this->setProductName($productName);
        $this->setProductAmount($productAmount);
        $this->setProductPrice($productPrice);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductPrice(): ?float
    {
        return $this->productPrice;
    }

    public function setProductPrice(?float $productPrice): self
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    public function getProductAmount(): ?int
    {
        return $this->productAmount;
    }

    public function setProductAmount(?int $productAmount): self
    {
        $this->productAmount = $productAmount;

        return $this;
    }

    public function getObjednavka(): ?Objednavka
    {
        return $this->objednavka;
    }

    public function setObjednavka(?Objednavka $objednavka): self
    {
        $this->objednavka = $objednavka;

        return $this;
    }
}
