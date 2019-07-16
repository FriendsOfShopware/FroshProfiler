<?php

namespace FroshProfiler\Components\Struct;

class Tax
{
    /**
     * @var float
     */
    private $taxRate;

    /**
     * @var float
     */
    private $sumOfCart;

    /**
     * @var float
     */
    private $proportionOfCart;

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function setTaxRate(float $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getSumOfCart(): float
    {
        return $this->sumOfCart;
    }

    public function setSumOfCart(float $sumOfCart): self
    {
        $this->sumOfCart = $sumOfCart;

        return $this;
    }

    public function getProportionOfCart(): float
    {
        return $this->proportionOfCart;
    }

    public function setProportionOfCart(float $proportionOfCart): self
    {
        $this->proportionOfCart = $proportionOfCart;

        return $this;
    }
}
