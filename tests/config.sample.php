<?php

/** @param string $base_uri ~ default: https://app.salsify.com/api/v1/ */
define('API_BASE_URI', 'https://app.salsify.com/api/v1/');

/**
 * @param string $salsify_org_id ~ the organization ID which is unique to each Salsify app instance. The org ID can be found after /orgs/ in the
 * URL path for your Salsify organization, eg. in https://app.salsify.com/app/orgs/9-99999-9999-9999-9999-999999999/products
 * the org ID is 9-99999-9999-9999-9999-999999999.
 */
define('API_ORG_ID', '');

/** @param string $token ~ see https://help.salsify.com/help/getting-started-api-auth */
define('API_TOKEN', '');

// A valid ID of the digital asset list, look in the URL, to run export tests against
define('LIST_ID_DIGITAL_ASSET', '');

// A valid ID of the product list, look in the URL, to run export tests against
define('LIST_ID_PRODUCTS', '');