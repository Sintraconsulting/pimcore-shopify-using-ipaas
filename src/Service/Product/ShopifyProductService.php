<?php

namespace SyncShopifyBundle\Service\Product;

use Doctrine\DBAL\Connection;
use Exception;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use SyncShopifyBundle\Abstract\AbstractShopifyService;
use SyncShopifyBundle\Model\Product\ShopifyProduct;
use Throwable;
use Traversable;

class ShopifyProductService extends AbstractShopifyService
{
    /** @var IShopifyProductMapper[] $productMappers */
    private array $productMappers;

    public function __construct(
        Connection      $connection,
        LoggerInterface $logger,
        #[TaggedIterator(tag: IShopifyProductMapper::MAPPER_TAG, indexAttribute: 'key')]
        iterable        $productMappers)
    {
        parent::__construct($connection, $logger);
        $this->productMappers = $productMappers instanceof Traversable ? iterator_to_array($productMappers) : $productMappers;
    }

    public function getProductsToSync(string $mapperServiceKey, int $limit): array
    {
        $mapperService = $this->getMapperService($mapperServiceKey);
        $productClassId = $mapperService->getProductClassId();

        $productIds = $this->getProductIds($productClassId, $mapperServiceKey);

        $newModificationDate = null;
        $mappedProducts = [];
        foreach ($productIds as $productId) {
            try {
                $product = Concrete::getById($productId['id'], ['force' => true]);
                $shopifyModelArray = $this->getMappedProductArray($mapperService, $product);

                if ($this->upsertProductEtag($productId['id'], $shopifyModelArray, $mapperServiceKey)) {
                    $mappedProducts[] = $shopifyModelArray;
                }

                $newModificationDate = $productId['mostRecentModificationDate'];
                if (count($mappedProducts) == $limit) {
                    break;
                }
            } catch (Throwable $th) {
                $this->logger->error("Error mapping product id: {$productId['id']}, mapper service key: {$mapperServiceKey}, 
                error message: {$th->getMessage()}");
            }
        }

        $this->upsertLastModificationDate($mapperServiceKey, $newModificationDate);

        return $mappedProducts;
    }

    private function getMapperService(string $mapperServiceKey): IShopifyProductMapper
    {
        $service = current(array_filter($this->productMappers, function ($productMapper) use ($mapperServiceKey) {
            return $productMapper->getMapperServiceKey() === $mapperServiceKey;
        }));

        if (empty($service)) {
            throw new Exception("Unable to find a mapper service with key equal to '$mapperServiceKey', 
                please check if the service is registered with the correct key");
        }

        return $service;
    }

    private function getMappedProductArray(IShopifyProductMapper $mapperService, Concrete $product): array
    {
        $shopifyProductModel = new ShopifyProduct();
        $shopifyProductModel = $mapperService->getMappedProduct($shopifyProductModel, $product);
        return $shopifyProductModel->getAsArray();
    }
}