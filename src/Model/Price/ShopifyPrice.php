<?php

namespace SyncShopifyBundle\Model\Price;

use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyPrice implements IShopifyModel
{
    private string $sku;
    private ?string $priceListId;
    private ?string $marketId;
    private float $price;
    private float $compareAtPrice;

    public function getAsArray(): array
    {
        $result = [
            'sku' => $this->sku,
            'price' => $this->price,
            'compareAtPrice' => $this->compareAtPrice,
        ];

        if (!empty($this->priceListId)) {
            $result['priceListId'] = $this->priceListId;
        }
        if (!empty($this->marketId)) {
            $result['marketId'] = $this->marketId;
        }

        return $result;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getPriceListId(): ?string
    {
        return $this->priceListId;
    }

    public function setPriceListId(?string $priceListId): void
    {
        $this->priceListId = $priceListId;
    }

    public function getMarketId(): ?string
    {
        return $this->marketId;
    }

    public function setMarketId(?string $marketId): void
    {
        $this->marketId = $marketId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getCompareAtPrice(): float
    {
        return $this->compareAtPrice;
    }

    public function setCompareAtPrice(float $compareAtPrice): void
    {
        $this->compareAtPrice = $compareAtPrice;
    }
}