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
* If you need to simulate Fake data http://climate.thephpleague.com/ 

## Features

###Products

Note these methods may avoid workflows and channels  
[\LCI\Salsify\Product](src/Product.php) [Examples](tests/ProductTest.php)

- [x] get() ~ raw/current data
- [x] create()
- [x] update()
- [x] delete()

###Property

[\LCI\Salsify\Property](src/Property.php) [Examples](tests/PropertyTest.php)

- [x] load()
- [x] create() 
- [x] update() 
- [x] delete()
- [ ] getValues()

###BulkProperties
@TODO

- [ ] getMany()

###Digital Asset

[\LCI\Salsify\Asset](src/Asset.php) and examples in [tests/AssetTest.php](tests/AssetTest.php)

- [x] get() Read
- [x] create()
- [x] update()
- [ ] delete() ~ fails tests
- [ ] refresh() ~ fails tests, needed if the source image gets updated but has the same URL 
to trigger Salsify to reprocess

###Channel

[\LCI\Salsify\Channel](src/Channel.php) and examples in [tests/ChannelTest.php](tests/ChannelTest.php)

- [x] getChannelData($channel_id)
- [x] saveLatestChannel($channel_id, $full_file_path)

###RawExports

[\LCI\Salsify\RawExports](src/RawExports.php) and examples in [tests/RawExportTest.php](tests/RawExportsTest.php)

- [x] initExportProductsList($list_id) ~ Export product list
- [x] initExportDigitalAssetsList($list_id) ~ Export a digital assets list
- [x] initExport() ~ used with setFilter and related to run custom exports, note you will have to figure out what those should be.
- [x] getExportRunStatus($export_id) retrieve status of export
- [x] saveExportReport($export_id, $full_file_name) ~ save report to disk

###RawExports

[\LCI\Salsify\RawExports](src/RawExports.php) and examples in [tests/RawExportTest.php](tests/RawExportsTest.php)


###Helpers

- [\LCI\Salsify\Helpers\ImageTransformation.php](src/Helpers/ImageTransformation.php) and examples in 
 [tests/ImageTransformationTest.php](tests/ImageTransformationTest.php)
-  [\LCI\Salsify\Helpers\HTML.php](src/Helpers/HTML.php) and examples in 
   [tests/HTMLHelperTest.php](tests/HTMLHelperTest.php)