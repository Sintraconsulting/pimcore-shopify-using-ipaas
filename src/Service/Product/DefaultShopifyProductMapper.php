<?php

namespace SyncShopifyBundle\Service\Product;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\ImageInfo;
use Pimcore\Model\DataObject\Product;
use Pimcore\Tool;
use SyncShopifyBundle\Model\Product\ShopifyProduct;
use SyncShopifyBundle\Model\Product\ShopifyProductMedia;
use SyncShopifyBundle\Model\Product\ShopifyProductVariant;

class DefaultShopifyProductMapper implements IShopifyProductMapper
{
    const DEFAULT_MAPPER_SERVICE_KEY = 'default_shopify_product';
    const PRODUCT_CLASS_ID = 'DEFAULT_PROD';

    public function getMapperServiceKey(): string
    {
        return self::DEFAULT_MAPPER_SERVICE_KEY;
    }

    public function getProductClassId(): string
    {
        return self::PRODUCT_CLASS_ID;
    }

    public function getMappedProduct(ShopifyProduct $shopifyProductModel, Concrete $product): ShopifyProduct
    {
        /** @var Product $product */

        $shopifyProductModel->setSku($product->getSku());
        $shopifyProductModel->setTitle($product->getName("it"));
        $shopifyProductModel->setDescription($product->getDescription());
        $shopifyProductModel->setPrice($product->getPrice_EUR());
        $shopifyProductModel->setHandle($product->getHandle("it"));
        $shopifyProductModel->addMedias($this->getMedias($product));
        $shopifyProductModel->addMetafield("made_in", $product->getMade_in());
        $shopifyProductModel->addTag($product->getBrand());

        /** @var Product[] $variants */
        $variants = $product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT])->load();
        if (!empty($variants)) {
            $shopifyProductModel->addVariants($this->getVariants($variants));
        }

        return $shopifyProductModel;
    }

    /**
     * @param Product $product
     * @return ShopifyProductMedia[]
     */
    private function getMedias(Product $product): array
    {
        $images = $product->getImages();
        if (empty($images)) {
            return [];
        }
        $medias = [];

        /** @var ImageInfo $image */
        foreach ($images->getItems() as $image) {
            $medias[] = new ShopifyProductMedia(Tool::getHostUrl() . $image->getImage()->getRealFullPath(), $image->getImage()->getFilename());
        }

        return $medias;
    }

    /**
     * @param Product[] $variants
     * @return ShopifyProductVariant[]
     */
    private function getVariants(array $variants): array
    {
        $shopifyProductVariants = [];

        foreach ($variants as $variant) {
            $shopifyProductVariant = new ShopifyProductVariant();
            $shopifyProductVariant->setSku($variant->getSku());
            $shopifyProductVariant->setBarcode($variant->getEan());
            $shopifyProductVariant->setPrice($variant->getPrice_EUR());
            $shopifyProductVariant->addMedias($this->getMedias($variant));
            $shopifyProductVariant->addOptionValue("color", $variant->getColor());
            $shopifyProductVariant->addOptionValue("size", $variant->getSize());

            $shopifyProductVariants[] = $shopifyProductVariant;
        }

        return $shopifyProductVariants;
    }
}