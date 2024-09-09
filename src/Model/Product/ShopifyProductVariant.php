<?php

namespace SyncShopifyBundle\Model\Product;

use stdClass;
use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyProductVariant extends AbstractShopifyProduct implements IShopifyModel
{
    private ?string $sortingNumber = null;
    private ?string $barcode = null;
    private array $optionValues = [];

    public function getAsArray(): array
    {
        $array = [
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'optionValues' => $this->optionValues,
            'media' => array_map(function (ShopifyProductMedia $media) {
                return $media->getAsArray();
            }, $this->media),
            'metafields' => !empty($this->metafields) ? $this->metafields : new stdClass(),
        ];

        if (!is_null($this->sortingNumber)) {
            $array['sortingNumber'] = $this->sortingNumber;
        }

        return $array;
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