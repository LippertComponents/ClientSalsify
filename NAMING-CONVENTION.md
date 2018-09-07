# LCI Naming Convention

**Draft**

Updated September 7, 2018

The goal of having a standardized naming convention is to produce consistency and allow all users to quickly gather the
basic information about the related product, asset or property. Placing a little more time up front to name elements will
allow easy growth and maintainability of the data. Please keep in mind that everything put in will one day need to be 
taken out or updated.

## SKUs

Most SKUs will come from an ERP and those SKUs need to be used. But sometimes they do not exist either the product is 
still in R&D, a Base Product is needed to group the various variants, or special useage making a product a category. 
If a follow the rules listed below.

**General**

- Must not have an ERP SKU
- lowercase
- Use hyphens instead of spaces 
- No other special characters, only -, 0-9 and a-z.

### Base Product 

Also referred to as a parent product. Use the general rules above and 

- Prefix with parent  
Example: **parent**-product-name

### Product as a Category

- Its purpose is to holds category copy
- Set property is_product_category=Yes

Use the general rules above and 

- Prefix with category  
Example: **category**-category-name
- Optional region, EU  
Example: **category-eu**-category-name
- If a child category prefix with category_parent SKU  
Example: **category-parent-category**-child-name

### R&D

Use the general rules above and 

- Prefix with rd  
Example: **rd**-proposed-product-name

### Other

ERPs that are not integrated

Prefix with appropriate differentiator  
Example: **tm**-product-name 


## Product Names

Name (String – Single text line – Proper Format)
The name or title should be a literal description of what the product is, and limited to the minimum identifying characteristics/properties

- No other special characters, only -, 0-9, a-z and ().
- See Trademarks below
- Use universal descriptions/terms for component parts where possible and avoid application/utilization terms 
(that info goes in the details field). For instance, the bolt that connects electric brake assemblies to the axle 
mounting flange is sometimes referred to as a ‘brake bolt’, but ‘hex bolt’ is the most universal, literal, and best way 
to describe that part. It’s very likely that part is used for more than just the brake application
- For products configured around a single identifying attribute, like color, size, etc. always list that property after 
a hyphen (Example: Awning Cradle – Black) or semicolon if the leading data ends with or following data starts with 
measurements (Example: Axle Hanger; 4-1/4”)
- For products configured around multiple attributes, breakup two identifying attributes by hyphen followed by 
parenthesis {Example: 1/4" Hydraulic Fitting - 90° Elbow (6801-04-04)} and breakup three identifying attributes by 
hyphen first, then semicolon, and finally parenthesis  
{Example: Hydraulic Jack Assembly - High Mount; 22,000 lbs. (12" Foot Pad)}
- Parts which are identified by more than three attributes, like many awing parts, won’t follow the above format 
- Avoid using conditional/comparative describing words (new style, old version, etc.), Lippert specific acronyms or 
abbreviations (EIRC or Electric Independent Room Control, XMC or Cross Member C-Shape, etc.), or coach specific info (Winnebago Only, FR34, etc.)


## Trademarks, Registered Trademarks & Copyright

For plain text fields do not include special or HTML characters. Supported list is below. 
It is the responsibility of the rendering page to turn it into HTML or correct coding or use a
[Saslify Formula](https://help.salsify.com/help/working-with-formulas).

 - Copyright -> ```(C)```
 - Registered Trademark -> ```(R)```
 - Trademark -> ```(TM)```

## Digital Assets

### General rules for all Asset names

- ~~All lowercase~~
- Human understandable
- Hyphens (-) should be used in place of spaces
- No other special characters, only -, 0-9 and a-z
- The name should be short and contain only the most basic identifying info, literally only enough to convey what it is
- Descriptor chose: one left, right, top, bottom, front, or back. 
- Additional descriptor if appropriate: 
  - main 
  - packaging
  - display
  - campground
  - installation
  - TBD... 

### Assets directly tied to a single SKU (base/saleable)

Includes 3 parts, each separated by an underscore: **SKU_Name_Descriptor** ~~**SKU_Name_Number**~~

- Use the General rules defined above for the Name
- ~~The number section always needs at least three (3) places (###) and start with one (001)~~
- See Descriptor defined above in General

| Good        | Bad           |
| ------------- | ------------- |
| 123456_Hex-Bolt_front_main.jpg | 123456- Ground Control Jack Mounting Bolt 1.jpg |
| 987654_Lug-Nut_right.jpg | 987654_9-16-Tapered-Lug-Nut_001.jpg |
| 555666_Hydraulic-Fitting_left.jpg | 555666 – swivel elbow hydraulic fitting – front 1.jpg |

### Assets tied to more than one SKU

**multi_sku_Name_Descriptor**

- Use the General rules defined above for the Name
- See Descriptor defined above in General

| Good        | Bad           |
| ------------- | ------------- |
| multi_sku_Pin-Box-Family_front_display.jpg | Pin Boxes #10.jpg |
 
## Properties

Before adding please follow

- [DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself)
- [KISS](https://en.wikipedia.org/wiki/KISS_principle)

### ID 

- Clear and consise 
- Lowercase
- Underscores (_) should be used in place of spaces
- No other special characters, only _, 0-9 and a-z.
- Follow patterns
- Prefix with source_ if the data comes from an ERP system and is for reference.  
Example: source_name would relate to the name property with is always editable. Any property with the source prefix is 
not editable as it will be overwritten on the new ERP push.
- Prefix with outgoing system name if it is determined that only the property can only be used for one destination.  
Check with [Formulas](https://help.salsify.com/help/working-with-formulas) before creating. Example magento_attribute_type
- Languages other then English, prefix preferably with [IS0 2](https://www.sitepoint.com/iso-2-letter-language-codes/) if
unavailable then with [ISO 3](https://www.loc.gov/standards/iso639-2/php/code_list.php) or 
[Country Code](http://www.fincher.org/Utilities/CountryLanguageList.shtml) 

### Name

- Human readable

### Help text

- Always fill out

### Metadata, attach and fill out 

- property_created_by
- property_platform_usage