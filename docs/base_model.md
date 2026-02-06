---
layout: default
title: Base Model
---
{% include nav.html %}

# Base Model Documentation

# BaseModel

## Overview

The `BaseModel` is a base Eloquent model class in a Laravel application that extends `Btx\Query\Model`. It provides common functionality for handling model attributes, such as formatted timestamps, encoded IDs, and status-related labels. It automatically appends several attributes to the model's JSON representation.

This model integrates with:
- Laravel's Eloquent ORM
- Carbon for date formatting
- Vinkla's Hashids for ID encoding
- Custom traits like `HasOptionalRelation`

**Namespace:** `App\Models`  
**Extends:** `Btx\Query\Model`  
**Uses Traits:** `HasOptionalRelation`

## Properties

### Protected Properties
- `$hidden`: Array of attributes to hide from JSON output (currently commented out: `['id']`).
- `$appends`: Array of attributes to append to the model's array/JSON form (automatically merged in constructor with formatted dates, encoded ID, and status labels).

## Methods

### Public Methods

#### `__construct(array $attributes = [])`
Initializes the model and merges additional appends if the `appends` property exists.

- **Parameters:**
  - `$attributes`: `array` - Model attributes.
- **Description:** Calls parent constructor and appends common attributes like `created_at_formatted`, `updated_at_formatted`, `encode_id`, `status_label`, `status_type`, `active_type`, and `color_status_label`.

#### `getCreatedAtFormattedAttribute()`
Returns the formatted creation date in Indonesian locale.

- **Returns:** `string|null` - Formatted date string (e.g., "Senin, 01 Jan 2023 12:00") or null if not set.
- **Description:** Uses Carbon to parse and format `created_at` with locale 'id'.

#### `getUpdatedAtFormattedAttribute()`
Returns the formatted update date in Indonesian locale.

- **Returns:** `string|null` - Formatted date string (e.g., "Senin, 01 Jan 2023 12:00") or null if not set.
- **Description:** Uses Carbon to parse and format `updated_at` with locale 'id'.

#### `getEncodeIdAttribute()`
Returns the encoded ID using Hashids.

- **Returns:** `string|null` - Encoded ID string or null if ID is not set.
- **Description:** Encodes the model's `id` for secure representation.

#### `getStatusLabelAttribute()`
Returns a human-readable status label based on the `status` attribute.

- **Returns:** `string|null` - "Aktif" if status is true, "Tidak Aktif" if false, or null if not set.
- **Description:** Checks if `status` is set and returns appropriate label.

#### `getStatusTypeAttribute()`
Returns a status type for UI purposes based on the `status` attribute.

- **Returns:** `string|null` - "success" if status is true, "error" if false, or null if not set.
- **Description:** Maps status to Quasar/Vue.js style types.

#### `getActiveTypeAttribute()`
Returns an active type based on the `active` attribute (note: seems to check `active` but method name suggests `status`).

- **Returns:** `string|null` - "success" if active is true, "error" if false, or null if not set.
- **Description:** Similar to `getStatusTypeAttribute` but uses `active` field.

#### `getColorStatusLabelAttribute()`
Returns a color label for status based on configuration.

- **Returns:** `string` - Color string from config (e.g., for active/status) or empty string if not set.
- **Description:** Retrieves color from `config('ihandcashier.status')` based on `active` or `status` attribute.

## Usage Notes

- Extend this class in your specific models to inherit common attribute formatting and encoding.
- The appended attributes are automatically included in JSON responses, providing formatted dates, encoded IDs, and status labels.
- Ensure the `ihandcashier.status` config is set for color mappings.
- Use `HasOptionalRelation` trait for optional relationships if needed.
