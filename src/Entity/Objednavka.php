<?php

namespace App\Entity;

use App\Entity\Payment;
use App\Entity\Country;
use App\Entity\Delivery;
use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ObjednavkaRepository")
 */
class Objednavka
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 9, max = 16)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 50)
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 50)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 50)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     * @Assert\Length(
     *         min = 5,
     *         max = 5
     * )
     */
    private $psc;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @Assert\NotBlank()
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Delivery")
     * @Assert\NotBlank()
     */
    private $delivery;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Payment")
     * @Assert\NotBlank()
     */
    private $payment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $productPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deliveryPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $paymentPriceCZK;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $paymentPriceEUR;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalPrice;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $goods = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShoppingBag", mappedBy="objednavka")
     */
    private $ShoppingBag;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Choice({"Nová", "Potvrzená", "Odeslaná", "Zrušená"})
     */
    private $state;

    public function __construct()
    {
        $this->ShoppingBag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPsc(): ?string
    {
        return $this->psc;
    }

    public function setPsc(?string $psc): self
    {
        $this->psc = $psc;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getDeliveryPrice(): ?float
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(?float $deliveryPrice): self
    {
        $this->deliveryPrice = $deliveryPrice;

        return $this;
    }

    public function getPaymentPriceCZK(): ?float
    {
        return $this->paymentPriceCZK;
    }

    public function setPaymentPriceCZK(?float $paymentPriceCZK): self
    {
        $this->paymentPriceCZK = $paymentPriceCZK;

        return $this;
    }

    public function getPaymentPriceEUR(): ?float
    {
        return $this->paymentPriceEUR;
    }

    public function setPaymentPriceEUR(?float $paymentPriceEUR): self
    {
        $this->paymentPriceEUR = $paymentPriceEUR;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getGoods(): ?array
    {
        return $this->goods;
    }

    public function setGoods(?array $goods): self
    {
        $this->goods = $goods;

        return $this;
    }

    /**
     * @return Collection|ShoppingBag[]
     */
    public function getShoppingBag(): Collection
    {
        return $this->ShoppingBag;
    }

    public function addShoppingBag(ShoppingBag $shoppingBag): self
    {
        if (!$this->ShoppingBag->contains($shoppingBag)) {
            $this->ShoppingBag[] = $shoppingBag;
            $shoppingBag->setObjednavka($this);
        }

        return $this;
    }

    public function removeShoppingBag(ShoppingBag $shoppingBag): self
    {
        if ($this->ShoppingBag->contains($shoppingBag)) {
            $this->ShoppingBag->removeElement($shoppingBag);
            // set the owning side to null (unless already changed)
            if ($shoppingBag->getObjednavka() === $this) {
                $shoppingBag->setObjednavka(null);
            }
        }

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
