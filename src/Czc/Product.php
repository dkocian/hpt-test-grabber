<?php
declare(strict_types=1);

namespace HPT\Czc;

class Product
{
    /** @var float|null */
    private $price;

    /** @var string|null */
    private $name;

    /** @var integer|null */
    private $rating;

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return self
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * @param float|null $rating
     * @return self
     */
    public function setRating(?float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}