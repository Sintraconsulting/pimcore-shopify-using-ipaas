<?php

namespace SyncShopifyBundle\Service\Translation;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product;
use SyncShopifyBundle\Model\Translation\ShopifyTranslation;
use SyncShopifyBundle\Model\Translation\ShopifyVariantTranslation;

class DefaultShopifyTranslationMapper implements IShopifyTranslationMapper
{
    const DEFAULT_MAPPER_SERVICE_KEY = 'default_shopify_translation';
    const PRODUCT_CLASS_ID = 'DEFAULT_PROD';

    public function getMapperServiceKey(): string
    {
        return self::DEFAULT_MAPPER_SERVICE_KEY;
    }

    public function getProductClassId(): string
    {
        return self::PRODUCT_CLASS_ID;
    }

    public function getMappedTranslation(ShopifyTranslation $shopifyTranslationModel, Concrete $product): ShopifyTranslation
    {
        /** @var Product $product */

        $shopifyTranslationModel->setSku($product->getSku());

        $shopifyTranslationModel->addTitle($product->getName("en"), "en");
        $shopifyTranslationModel->addTitle($product->getName("de"), "de");
        $shopifyTranslationModel->addTitle($product->getName("fr"), "fr");

        $shopifyTranslationModel->addDescription($product->getDescription("en"), "en");
        $shopifyTranslationModel->addDescription($product->getDescription("de"), "de");
        $shopifyTranslationModel->addDescription($product->getDescription("fr"), "fr");

        $shopifyTranslationModel->addMetafield("short_description", $product->getShort_description("en"), "en");
        $shopifyTranslationModel->addMetafield("short_description", $product->getShort_description("de"), "de");
        $shopifyTranslationModel->addMetafield("short_description", $product->getShort_description("fr"), "fr");

        /** @var Product[] $variants */
        $variants = $product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT])->load();
        if (!empty($variants)) {
            $shopifyTranslationModel->addVariants($this->getVariants($variants));
        }

        return $shopifyTranslationModel;
    }

    /**
     * @param Product[] $variants
     * @return ShopifyVariantTranslation[]
     */
    private function getVariants(array $variants): array
    {
        $shopifyTranslationVariants = [];

        foreach ($variants as $variant) {
            $shopifyTranslationVariant = new ShopifyVariantTranslation();

            $shopifyTranslationVariant->setSku($variant->getSku());

            $shopifyTranslationVariant->addMetafield("short_description", $variant->getShort_description("en"), "en");
            $shopifyTranslationVariant->addMetafield("short_description", $variant->getShort_description("de"), "de");
            $shopifyTranslationVariant->addMetafield("short_description", $variant->getShort_description("fr"), "fr");

            $shopifyTranslationVariant->addOptionValue("color", $variant->getColor("en"), "en");
            $shopifyTranslationVariant->addOptionValue("color", $variant->getColor("de"), "de");
            $shopifyTranslationVariant->addOptionValue("color", $variant->getColor("fr"), "fr");
            $shopifyTranslationVariant->addOptionValue("size", $variant->getSize("en"), "en");
            $shopifyTranslationVariant->addOptionValue("size", $variant->getSize("de"), "de");
            $shopifyTranslationVariant->addOptionValue("size", $variant->getSize("fr"), "fr");

            $shopifyTranslationVariants[] = $shopifyTranslationVariant;
        }

        return $shopifyTranslationVariants;
    }
}