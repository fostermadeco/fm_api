# Foster Made API

An ExpressionEngine extension to handle creating API endpoints for installed add-ons.

## Usage

In order to enable an add-on for use via the API extensions:
1. Open your site's config file
2. Add to the config's $config['apiEndpoints'] array (use $env_config w/ FocusLab's master config)
3. Place the appropriate library in the PATH_THIRD/fm_api/libraries directory

API calls can be made via GET, POST, DELETE, or PUT requests.

## How calls are made:

URLs are built out as such:
```
https://[site_url]/api/[library]
```

The request method determines the function called. As an example:

A GET request made to `http://demo-site.dev/api/favorites` will look for the `favorites` library
and run the `getFavorites()` method.

A POST request made to `http://demo-site.dev/api/comments` will look for the `comments` library
and run the `postComments()` method.