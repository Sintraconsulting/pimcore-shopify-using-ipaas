<?php

namespace SyncShopifyBundle\Model\Product;

use SyncShopifyBundle\Model\IShopifyModel;

class ShopifyProductMedia implements IShopifyModel
{
    private string $url;
    private string $alternativeText;

    public function __construct(string $url, string $alternativeText)
    {
        $this->url = $url;
        $this->alternativeText = $alternativeText;
    }

    public function getAsArray(): array
    {
        return [
            'url' => $this->url,
            'alternativeText' => $this->alternativeText,
        ];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getAlternativeText(): string
    {
        return $this->alternativeText;
    }

    public function setAlternativeText(string $alternativeText): void
    {
        $this->alternativeText = $alternativeText;
    }
}