<?php

namespace SyncShopifyBundle\Model\Product;

use stdClass;
use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyProduct extends AbstractShopifyProduct implements IShopifyModel
{

    private ?string $title = null;
    private ?string $description = null;
    private ?string $vendor = null;
    private ?string $handle = null;
    private ?string $type = null;
    private array $markets = [];
    private array $tags = [];
    private array $files = [];

    /** @var ShopifyProductVariant[] $variants */
    private array $variants = [];

    public function getAsArray(): array
    {
        return [
            'sku' => $this->sku,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'vendor' => $this->vendor,
            'handle' => $this->handle,
            'type' => $this->type,
            'markets' => $this->markets,
            'tags' => $this->tags,
            'files' => !empty($this->files) ? $this->files : new stdClass(),
            'metafields' => !empty($this->metafields) ? $this->metafields : new stdClass(),
            'media' => array_map(function (ShopifyProductMedia $media) {
                return $media->getAsArray();
            }, $this->media),
            'variants' => array_map(function (ShopifyProductVariant $variant) {
                return $variant->getAsArray();
            }, $this->variants),
        ];
    }

    /**
     * @param ShopifyProductVariant[] $variants
     * @return void
     */
    public function addVariants(array $variants): void
    {
        $this->variants = array_merge($this->variants, $variants);
    }

    public function addVariant(ShopifyProductVariant $variant): void
    {
        $this->variants[] = $variant;
    }

    public function addFile(string $fileMetafieldKey, string $fileUrl): void
    {
        $this->files[$fileMetafieldKey] = $fileUrl;
    }

    public function addMarket(string $marketId): void
    {
        $this->markets[] = $marketId;
    }

    /**
     * @param string[] $markets
     * @return void
     */
    public function addMarkets(array $markets): void
    {
        $markets = array_filter($markets, function ($market) {
            return !empty($market);
        });
        $this->markets = array_merge($this->markets, $markets);
    }

    public function addTag(?string $tag): void
    {
        if (!empty($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @param string[] $tags
     * @return void
     */
    public function addTags(array $tags): void
    {
        $tags = array_filter($tags, function ($tag) {
            return !empty($tag);
        });
        $this->tags = array_merge($this->tags, $tags);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setHandle(?string $handle): void
    {
        $this->handle = $handle;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getMarkets(): array
    {
        return $this->markets;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}