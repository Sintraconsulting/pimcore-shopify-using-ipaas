<?php

namespace SyncShopifyBundle\Model\Translation;

use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyTranslation implements IShopifyModel
{
    private ?string $sku = null;
    private array $title = [];
    private array $bodyHtml = [];
    private array $handle = [];
    private array $productType = [];
    private array $files = [];
    private array $metafields = [];

    /** @var ShopifyVariantTranslation[] $variants */
    private array $variants = [];

    public function getAsArray(): array
    {
        return [
            'sku' => $this->sku,
            'title' => $this->title,
            'body_html' => $this->bodyHtml,
            'handle' => $this->handle,
            'product_type' => $this->productType,
            'files' => $this->files,
            'metafields' => $this->metafields,
            'variants' => array_map(function (ShopifyVariantTranslation $variant) {
                return $variant->getAsArray();
            }, $this->variants),

        ];
    }

    public function addTitle(string $title, string $language): void
    {
        $this->title[$language] = $title;
    }

    public function addBodyHtml(string $bodyHtml, string $language): void
    {
        $this->bodyHtml[$language] = $bodyHtml;
    }

    public function addHandle(string $handle, string $language): void
    {
        $this->handle[$language] = $handle;
    }

    public function addProductType(string $productType, string $language): void
    {
        $this->productType[$language] = $productType;
    }

    public function addFile(string $fileMetafieldKey, string $fileUrl, string $language): void
    {
        $this->files[$fileMetafieldKey][$language] = $fileUrl;
    }

    public function addMetafield(string $key, string $value, string $language): void
    {
        $this->metafields[$key][$language] = $value;
    }

    public function addVariant(ShopifyVariantTranslation $variant): void
    {
        $this->variants[] = $variant;
    }

    /**
     * @param ShopifyVariantTranslation[] $variants
     * @return void
     */
    public function addVariants(array $variants): void
    {
        $this->variants = array_merge($this->variants, $variants);
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getMetafields(): array
    {
        return $this->metafields;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function getBodyHtml(): array
    {
        return $this->bodyHtml;
    }

    public function getHandle(): array
    {
        return $this->handle;
    }

    public function getProductType(): array
    {
        return $this->productType;
    }

}