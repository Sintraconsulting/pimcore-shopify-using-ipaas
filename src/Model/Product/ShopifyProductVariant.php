<?php

namespace SyncShopifyBundle\Model\Product;

use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyProductVariant extends AbstractShopifyProduct implements IShopifyModel
{
    private ?string $sortingNumber = null;
    private ?string $barcode = null;
    private array $optionValues = [];

    public function getAsArray(): array
    {
        return [
            'sku' => $this->sku,
            'sortingNumber' => $this->sortingNumber,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'optionValues' => $this->optionValues,
            'media' => array_map(function (ShopifyProductMedia $media) {
                return $media->getAsArray();
            }, $this->media),
            'metafields' => $this->metafields,
        ];
    }

    public function getSortingNumber(): string
    {
        return $this->sortingNumber;
    }

    public function setSortingNumber(?string $sortingNumber): void
    {
        $this->sortingNumber = $sortingNumber;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): void
    {
        $this->barcode = $barcode;
    }

    public function getOptionValues(): array
    {
        return $this->optionValues;
    }

    public function addOptionValue(?string $key, ?string $value): void
    {
        if (!empty($key) && !empty($value)) {
            $this->optionValues[$key] = $value;
        }
    }
}