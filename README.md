# REDAXO Headless - SPA + REDAXO = :heart:
The goal of the REDAXO Headless ecosystem is to offer a developer approach to integrating SPAs into REDAXO. The setup will usually consist out of the following three sub-systems:
- [REDAXO AddOn](#redaxo-addon)
- [Example SPA](https://github.com/jelleschutter/redaxo_headless_vue_frontend)
- [GitHub Action for deployment](https://github.com/jelleschutter/redaxo-headless-deploy)
## REDAXO AddOn
The REDAXO AddOn provides API endpoints to allow easy access to page content from the SPA. Currently there are three endpoints:
- [Navigation](#navigation)
- [Content](#content)
- [Deploy](#deploy)
### Navigation
Using the navigation endpoint you can get the information needed for a nav bar.
**URL** | **Method**
--- | ---
/?rex-api-call=headless_nav | `GET`
#### URL Params
**Name** | **Type** | **Required** | **Default**
--- | --- | --- | ---
path | string | Yes | -
levels | number | No | 2
#### Example Response
```
[
  {
    "id": 1,
    "link": "/",
    "name": "Home",
    "current": true,
    "active": true
  },
  {
    "id": 2,
    "link": "/test/",
    "name": "Test Category",
    "current": false,
    "active": false,
    "children": [
      {
        "id": 2,
        "link": "/test/",
        "name": "Test Article",
        "current": false,
        "active": false
      },
      {
        "id": 3,
        "link": "/test/new-article/",
        "name": "New Article",
        "current": false,
        "active": false
      }
    ]
  }
]
```
### Navigation
Using the navigation endpoint you can get the information needed for a nav bar.
**URL** | **Method**
--- | ---
/?rex-api-call=headless_content | `GET`
#### URL Params
**Name** | **Type** | **Required** | **Note**
--- | --- | --- | ---
path | string | Yes | Path relative to root without leading slash. Therefore the root article is requested by leaving the path empty.
#### Example Response
```
{
  "meta": {
    "title": "Home / REDAXO",
    "description": ""
  },
  "title": "Home",
  "content": "<p>This is some content.</p>"
}
```

### Deploy
The deploy endpoint is a part of the deploy plugin which has to be enabled separately on the addon system page.
The deployment function used by the headless deployment github action. For more info see: [redaxo-headless-deploy](https://github.com/jelleschutter/redaxo-headless-deploy)
**URL** | **Method**
--- | ---
/?rex-api-call=headless_deploy | `POST`
#### POST Params
**Name** | **Type** | **Required** | **Notes**
--- | --- | --- | ---
token | string | Yes | Defined in the REDAXO backend in the deploy plugin.
file | file | Yes | ZIP file containing the built SPA code at the root.
#### Example Response
```
{
  "msg": "Successfully updated content!"
}
```
