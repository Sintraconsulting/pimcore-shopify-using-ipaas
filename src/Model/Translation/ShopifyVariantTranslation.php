<?php

namespace SyncShopifyBundle\Model\Translation;

use stdClass;
use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyVariantTranslation implements IShopifyModel
{
    private string $sku;
    private array $files = [];
    private array $metafields = [];
    private array $optionValues = [];

    public function getAsArray(): array
    {
        return [
            'sku' => $this->sku,
            'files' => !empty($this->files) ? $this->files : new stdClass(),
            'metafields' => !empty($this->metafields) ? $this->metafields : new stdClass(),
            'optionValues' => $this->optionValues,
        ];
    }

    public function addFile(string $fileMetafieldKey, ?string $fileUrl, string $language): void
    {
        $this->files[$fileMetafieldKey][$language] = $fileUrl;
    }

    public function addMetafield(string $key, ?string $value, string $language): void
    {
        $this->metafields[$key][$language] = $value;
    }

    public function addOptionValue(string $key, ?string $value, string $language): void
    {
        $this->optionValues[$key][$language] = $value;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getMetafields(): array
    {
        return $this->metafields;
    }

    public function getOptionValues(): array
    {
        return $this->optionValues;
    }

}