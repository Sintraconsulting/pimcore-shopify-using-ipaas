<?php

namespace SyncShopifyBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use function dirname;

class SyncShopifyBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;

    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getInstaller(): ?InstallerInterface
    {
        $service = $this->container->get(Installer::class);
        if ($service instanceof Installer) {
            return $service;
        } else {
            return null;
        }
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/syncshopify/js/pimcore/startup.js'
        ];
    }

}