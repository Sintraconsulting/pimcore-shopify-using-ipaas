# Sync Shopify Bundle

Bundle Pimcore 11 per l'integrazione con IPaaS per la sincronizzazione di anagrafica, traduzioni e prezzi dei prodotti.

## Installazione

- Eseguire il comando `composer require sintra/pimcore-shopify-using-ipaas`
- Abilitare il bundle nel file `config/bundles.php`
  ``` php
  SyncShopifyBundle\SyncShopifyBundle::class => ['all' => true]
  ```
- Installare il bundle con il comando eseguito all'interno del
  container docker `bin/console pimcore:bundle:install SyncShopifyBundle`

## Sicurezza

Nel bundle è configurato un Authenticator Symfony per proteggere gli endpoint tramite un API Key.

- Inserire l'API Key nel file `.env` oppure nell'environment del `docker-compose.yaml`
  ``` yaml
   SYNC_SHOPIFY_BUNDLE_API_KEY: your-api-key
  ```

- Aggiungere la configurazione nel file `config/packages/security.yaml`

  ``` yaml
  security:
    firewalls:
      sync-shopify:
        pattern: ^/sync-shopify
        stateless: true
        custom_authenticators:
          - SyncShopifyBundle\Security\ApiKeyAuthenticator
   
    access_control:
      - { path: ^/sync-shopify, roles: IS_AUTHENTICATED_FULLY }
  ```
  E' possibile utilizzare un Authenticator custom sostituendo quello presente.