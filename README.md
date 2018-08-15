# Client Salsify

PHP library to communicate with the Salsify REST API. Salsify is a 
[PIM](https://en.wikipedia.org/wiki/Product_information_management "Product Information Management ") 
and a [DAM](https://en.wikipedia.org/wiki/Digital_asset_management "Digital Asset Management")

[Salsify documentation](https://help.salsify.com/help/api)
 
 
## Install

- [ ] Install via composer: composer require lci/clientsalsify
- [x] Or clone/copy files where you want then and then run composer require.

## Required Composer Packages

* http://docs.guzzlephp.org/en/latest/index.html
* If you need to simulate Fake data http://climate.thephpleague.com/ 

## Features

**Products**  
Note these methods may avoid workflows and channels  
[\LCI\Salsify\Product](src/Product.php) [Examples](tests/ProductTest.php)

- [x] get() ~ raw/current data
- [x] create()
- [x] update()
- [x] delete()

**Property**
[\LCI\Salsify\Property](src/Property.php) [Examples](tests/PropertyTest.php)

- [x] load()
- [x] create() 
- [x] update() 
- [x] delete()
- [ ] getValues()

**BulkProperties**
@TODO

- [ ] getMany()

**Digital Asset**

- [x] get() Read
- [x] create()
- [x] update()
- [ ] delete() ~ fails tests
- [ ] refresh() ~ fails tests, needed if the source image gets updated but has the same URL 
to trigger Salsify to reprocess
