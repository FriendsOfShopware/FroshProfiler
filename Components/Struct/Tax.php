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

    /**
     * @return float
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param float $taxRate
     * @return Tax
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return float
     */
    public function getSumOfCart()
    {
        return $this->sumOfCart;
    }

    /**
     * @param float $sumOfCart
     * @return Tax
     */
    public function setSumOfCart($sumOfCart)
    {
        $this->sumOfCart = $sumOfCart;
        return $this;
    }

    /**
     * @return float
     */
    public function getProportionOfCart()
    {
        return $this->proportionOfCart;
    }

    /**
     * @param float $proportionOfCart
     * @return Tax
     */
    public function setProportionOfCart($proportionOfCart)
    {
        $this->proportionOfCart = $proportionOfCart;
        return $this;
    }
}