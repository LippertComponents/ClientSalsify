# Client Salsify

PHP library to communicate with the Salsify REST API. Salsify is a 
[PIM](https://en.wikipedia.org/wiki/Product_information_management "Product Information Management ") 
and a [DAM](https://en.wikipedia.org/wiki/Digital_asset_management "Digital Asset Management")

[Salsify documentation](https://developers.salsify.com/)
 
 
## Install

- [x] Install via composer: composer require lci/clientsalsify
- [x] Or clone/copy files where you want then and then run composer require.

## Required Composer Packages

* http://docs.guzzlephp.org/en/latest/index.html

## Features

### Products

Note these methods may avoid workflows and channels  
[\LCI\Salsify\Product](src/Product.php) [Examples](tests/ProductTest.php)

- [x] get() ~ raw/current data
- [x] create()
- [x] update()
- [x] delete()

### Property

[\LCI\Salsify\Property](src/Property.php) [Examples](tests/PropertyTest.php)

- [x] load()
- [x] create() 
- [x] update() 
- [x] delete()
- [ ] getValues()

### BulkProperties
@TODO

- [ ] getMany()

### Digital Asset

- [\LCI\Salsify\DigitalAsset](src/DigitalAsset.php) and examples in [tests/DigitalAssetTest.php](tests/DigitalAssetTest.php)
- [\LCI\Salsify\BulkDigitalAsset](src/BulkDigitalAssets.php) and examples in [tests/BulkDigitalAssetsTest.php](tests/BulkDigitalAssetsTest.php)


- [x] get() Read
- [x] create()
- [x] update()
- [ ] delete() ~ fails tests
- [ ] refresh() ~ fails tests, needed if the source image gets updated but has the same URL 
to trigger Salsify to reprocess

### Channel

[\LCI\Salsify\Channel](src/Channel.php) and examples in [tests/ChannelTest.php](tests/ChannelTest.php)

- [x] getChannelData($channel_id)
- [x] saveLatestChannel($channel_id, $full_file_path)

### RawExports

[\LCI\Salsify\RawExports](src/RawExports.php) and examples in [tests/RawExportTest.php](tests/RawExportsTest.php)

- [x] initExportProductsList($list_id) ~ Export product list
- [x] initExportDigitalAssetsList($list_id) ~ Export a digital assets list
- [x] initExport() ~ used with setFilter and related to run custom exports, note you will have to figure out what those should be.
- [x] getExportRunStatus($export_id) retrieve status of export
- [x] saveExportReport($export_id, $full_file_name) ~ save report to disk

### Helpers

- [\LCI\Salsify\Helpers\ImageTransformation.php](src/Helpers/ImageTransformation.php) and examples in 
 [tests/ImageTransformationTest.php](tests/ImageTransformationTest.php)
-  [\LCI\Salsify\Helpers\HTML.php](src/Helpers/HTML.php) and examples in 
   [tests/HTMLHelperTest.php](tests/HTMLHelperTest.php)
   
#### Web Hooks

[Digital Asset Webhooks](https://developers.salsify.com/docs/digital-asset-webhooks)

Slim example: PS&-7 Webhook helper 

1. Create a route
2. See the example method below
3. Set up in Salsify, [subscribe to your new route](https://help.salsify.com/help/setting-up-product-change-alerts-in-salsify)

```php
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function digitalAssetWebHook(Request $request, Response $response, array $args)
    {
        $this->loadSalsify();
        $webhookHelper = new \LCI\Salsify\Helpers\Webhooks(
            $this->salsify,
            $request->getUri()
        );

        $webhookHelper
            ->setCertUrl($request->getHeaderLine('X-Salsify-Cert-Url'))
            ->setRequestBody($request->getBody())
            ->setRequestId($request->getHeaderLine('X-Salsify-Request-ID'))
            ->setSignature($request->getHeaderLine('X-Salsify-Signature-v1'))
            ->setTimestamp((int)$request->getHeaderLine('X-Salsify-Timestamp'));

        // the second paramater is a bool for whether or not to validate the SSL cert, this is still experimental
        // There are 3 validation checks before the SSL cert check
        if ($webhookHelper->verifyRequest($request->getHeaderLine('X-Salsify-Organization-ID'), false)) {
            
            $assets = $webhookHelper->getDigitalAssetsFromRequestBody();
            // Valid now do something:
            switch ($webhookHelper->getTriggerType()) {
                case 'add':
                    // new 
                    break;
                case 'change':
                    // this is an update to the item
                    break;
                case 'remove':
                    break;
            }
        };
        
        ...
    }
```
