<?php

namespace SyncShopifyBundle\Controller;

use Pimcore\Controller\FrontendController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SyncShopifyBundle\Service\Price\ShopifyPriceService;
use SyncShopifyBundle\Service\Product\ShopifyProductService;
use SyncShopifyBundle\Service\Translation\ShopifyTranslationService;
use Throwable;

#[Route('/sync-shopify')]
class SyncProductController extends FrontendController
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    #[Route('/product', methods: ['GET'])]
    public function getProductsAction(Request $request, ShopifyProductService $productService): JsonResponse
    {
        try {
            $mapperServiceKey = $request->get("mapper-service-key");
            $limit = (int)$request->get("limit");

            $products = $productService->getProductsToSync($mapperServiceKey, $limit);
            return new JsonResponse(["data" => $products]);
        } catch (Throwable $th) {
            $this->logger->error("Error to get products, message: {$th->getMessage()}");
            return new JsonResponse("Error: {$th->getMessage()}", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/translation', methods: ['GET'])]
    public function getTranslationsAction(Request $request, ShopifyTranslationService $translationService): JsonResponse
    {
        try {
            $mapperServiceKey = $request->get("mapper-service-key");
            $limit = (int)$request->get("limit");

            $translations = $translationService->getTranslationsToSync($mapperServiceKey, $limit);
            return new JsonResponse(["data" => $translations]);
        } catch (Throwable $th) {
            $this->logger->error("Error to get translations, message: {$th->getMessage()}");
            return new JsonResponse("Error: {$th->getMessage()}", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/price', methods: ['GET'])]
    public function getPricesAction(Request $request, ShopifyPriceService $priceService): JsonResponse
    {
        try {
            $mapperServiceKey = $request->get("mapper-service-key");
            $limit = (int)$request->get("limit");

            $prices = $priceService->getPricesToSync($mapperServiceKey, $limit);
            return new JsonResponse(["data" => $prices]);
        } catch (Throwable $th) {
            $this->logger->error("Error to get prices, message: {$th->getMessage()}");
            return new JsonResponse("Error: {$th->getMessage()}", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
