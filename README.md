# Active Plugin

WordPress plugin for managing license activation keys.

## Features

- Create and manage activation keys for plugins/themes
- API endpoints for license validation
- Simple admin interface
- Track license expiration dates
- Domain-specific license validation

## Installation

1. Upload the `active-plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the 'Active Plugin' menu in the admin sidebar to manage activation keys

## API Usage

### License Check Endpoint

```
GET /wp-json/license/v2/check
```

Parameters:
- License: The license key to validate
- Domain: The domain to validate against
- Soft_Id: The plugin ID to validate

Example response:
```json
{
  "error": 100,
  "success": true,
  "msg": "License: ABC123 - Hợp lệ",
  "plugin": {
    "ID": "my-plugin",
    "Name": "My Plugin",
    "row": 1
  },
  "license": {
    "License": "ABC123",
    "Domain": "example.com",
    "Status": "Active",
    "Expiry_Date": "01-01-2023"
  }
}
```

## Credits

Created by Trần Danh Trọng - [danhtrong.com](https://danhtrong.com)
