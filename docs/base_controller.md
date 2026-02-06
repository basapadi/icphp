---
layout: default
title: Base Controller
---
{% include nav.html %}

# Base Controller Documentation

# BaseController

## Overview

The `BaseController` is a base Laravel controller class that provides common functionality for handling grid views, forms, data management, access control, file uploads, and more. It is designed to be extended by specific controllers in a Laravel application, particularly for modules that require CRUD operations, data grids, and form handling.

This controller integrates with various libraries and custom components, such as:
- Laravel's Eloquent and Query Builder
- Spatie's Fractal for data transformation
- Vinkla's Hashids for ID encoding/decoding
- Custom traits like `HasQueryBuilder`, `QueryHelper`, etc.
- File upload utilities
- Audit logging

**Namespace:** `App\Http\Controllers`  
**Extends:** `Controller`  
**Uses Traits:** `HasQueryBuilder`, `QueryHelper`, `DataBuilder`, `BaseHelper`, `Services`

## Properties

### Private Properties
- `$_model`: Instance of the model (e.g., `Btx\Query\Model`).
- `$_columns`: Fractal instance for grid columns.
- `$_filterColumnsLike`: Array of column names for LIKE filtering.
- `$_filterParamLike`: String parameter for LIKE filtering.
- `$_queryBuilder`: Query builder instance.
- `$_multipleSelectGrid`: Boolean flag for enabling multiple select in grid (default: true).
- `$_form`: Array of form configurations.
- `$_createRules`: Array of validation rules for creation.
- `$_updateRules`: Array of validation rules for updates.
- `$_formData`: Array of default form data.
- `$_contextMenus`: Array of context menu items.
- `$_injectDataColumns`: Array of data to inject into columns.
- `$_exceptContextMenu`: Array of context menu keys to exclude.

### Protected Properties
- `$_module`: String representing the module name.
- `$_gridProperties`: Array of grid properties (e.g., filters, selections).
- `$_detailSchema`: Array for detail view schema.

## Methods

### Public Methods

#### `grid(Request $request)`
Handles grid requests to fetch data with filtering and pagination.

- **Parameters:**
  - `$request`: `Illuminate\Http\Request` - The HTTP request object.
- **Returns:** `\Illuminate\Http\JsonResponse` - JSON response with grid data.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Applies filters, fetches rows and total count, merges data if needed, sets default context menus, and returns grid configuration including rows, columns, properties, and detail schemas.

#### `form(Request $request)`
Handles form requests to fetch form configuration and data.

- **Parameters:**
  - `$request`: `Illuminate\Http\Request` - The HTTP request object.
- **Returns:** `\Illuminate\Http\JsonResponse` - JSON response with form sections, dialog, and data.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Processes form configurations using Fractal transformer and returns form data for creation.

#### `store(Request $request)`
Stores a new resource. (Currently incomplete: `//TODO:: Store default`)

- **Parameters:**
  - `$request`: `Illuminate\Http\Request` - The HTTP request object.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Checks access for creation but implementation is pending.

#### `delete(Request $request, $id)`
Deletes a resource by encoded ID.

- **Parameters:**
  - `$request`: `Illuminate\Http\Request` - The HTTP request object.
  - `$id`: `string` - Encoded ID of the resource.
- **Returns:** `\Illuminate\Http\JsonResponse` - JSON response indicating success or failure.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Decodes ID, loads relations, deletes the model, saves to trash, logs audit, and commits transaction. Rolls back on error.

#### `arrayToJson(array $array)`
Converts an array to a pretty-printed JSON response.

- **Parameters:**
  - `$array`: `array` - The array to convert.
- **Returns:** `\Illuminate\Http\JsonResponse` - JSON response of the array.
- **Description:** Utility method for returning arrays as JSON.

### Protected Methods

#### `setColumns(array $columns)`
Sets the columns for the grid response.

- **Parameters:**
  - `$columns`: `array` - Columns in the format required by `Btx\Query\Transformer::quasarColumn`.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setModel(string $model)`
Sets the model for the controller and initializes its query builder.

- **Parameters:**
  - `$model`: `string` - Fully qualified class name of the model.
- **Returns:** `\Illuminate\Database\Eloquent\Builder<\Btx\Query\Model>` - The query builder instance.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setFilterColumnsLike(array $columns, string $param)`
Sets custom filter columns and parameter for LIKE queries in the grid.

- **Parameters:**
  - `$columns`: `array` - List of column names to apply LIKE filter.
  - `$param`: `string` - The filter value.
- **Returns:** `void`
- **See:** https://btx.basapadi.com/query/request-query
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setMultipleSelectGrid(bool $value)`
Enables or disables multiple select in the grid view.

- **Parameters:**
  - `$value`: `bool` - Enable or disable flag.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `decodeId(string $encodeId)`
Decodes an encoded ID using Hashids.

- **Parameters:**
  - `$encodeId`: `string` - Encoded ID string.
- **Returns:** `mixed` - Decoded ID or null on failure.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setModule(string $module)`
Sets the module name for the controller.

- **Parameters:**
  - `$module`: `string` - Module name.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `allowAccessModule(string $module, string $action, bool $asBoolean = false)`
Checks access permission for a specific module action.

- **Parameters:**
  - `$module`: `string` - Module name.
  - `$action`: `string` - Action name (e.g., view, create, update).
  - `$asBoolean`: `bool` - If true, return boolean; otherwise abort with 401.
- **Returns:** `bool|null` - Access status or null.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Queries the database for role-based access and handles unauthorized access.

#### `allowAccess(string $action)`
Checks access for a given action and returns the action name if allowed, or an empty string if not.

- **Parameters:**
  - `$action`: `string` - Action name (e.g., view, create, edit, update, download).
- **Returns:** `string` - Action name or empty string.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setDataDefaultForm(array $fields, array $data = [])`
Sets the form fields and default data for the controller.

- **Parameters:**
  - `$fields`: `array` - Form fields.
  - `$data`: `array` - Default form data.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setDetailSchema(array $schema)`
Sets the detail schema for the view.

- **Parameters:**
  - `$schema`: `array` - Detail schema.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setGridProperties(array $properties)`
Sets grid properties.

- **Parameters:**
  - `$properties`: `array` - Properties like multipleSelect, filterDateRange, advanceFilter.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Available attributes: multipleSelect (bool, default: true), filterDateRange (bool, default: false), advanceFilter (bool, default: true).

#### `setContextMenu(array $contextMenu)`
Sets additional context menus on the grid.

- **Parameters:**
  - `$contextMenu`: `array` - Array of ContextMenu objects.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `clearContextMenu()`
Clears all context menus.

- **Returns:** `void`

#### `getDetailSchema($schema = null)`
Gets the detail schema from a JSON file.

- **Parameters:**
  - `$schema`: `string|null` - Schema name (defaults to module).
- **Returns:** `array` - Decoded schema.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getResourceForm($name)`
Gets the resource form configuration from a JSON file.

- **Parameters:**
  - `$name`: `string` - Form name.
- **Returns:** `array` - Decoded form.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getResourceDefaultContent($name)`
Gets default content from an HTML file.

- **Parameters:**
  - `$name`: `string` - Content name.
- **Returns:** `string` - File content or empty string.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getResourceEvaluationForm($name)`
Gets evaluation form from a JSON file.

- **Parameters:**
  - `$name`: `string` - Form name.
- **Returns:** `array` - Decoded form.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getResourceQuery($path)`
Gets resource query configuration from a file.

- **Parameters:**
  - `$path`: `string` - Path to the query file.
- **Returns:** `array` - Decoded query.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getResourceSql($path)`
Gets raw SQL from a file.

- **Parameters:**
  - `$path`: `string` - Path to the SQL file.
- **Returns:** `string` - File content.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getResourceStatic($name)`
Gets static resource data from a JSON file.

- **Parameters:**
  - `$name`: `string` - Static resource name.
- **Returns:** `array` - Decoded data.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setInjectDataColumn(array $data)`
Sets data columns to be injected into the grid columns.

- **Parameters:**
  - `$data`: `array` - Data to inject.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `setExceptContextMenu(array $contextmenukey)`
Sets exceptions for context menus.

- **Parameters:**
  - `$contextmenukey`: `array` - Array of default context menu keys (e.g., ['view','create','edit','delete']).
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `getColumns($filename = null)`
Gets the columns configuration for the grid, injecting data and filtering actions.

- **Parameters:**
  - `$filename`: `string|null` - Filename (defaults to module).
- **Returns:** `array` - Processed columns.
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `defaultContextMenu()`
Initializes default context menu items based on permissions.

- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
- **Description:** Adds menus like view, create, edit, delete, reload, and column editor based on access.

#### `privateUpload($filename, $path = '')`
Handles private file uploads.

- **Parameters:**
  - `$filename`: `string` - Field name.
  - `$path`: `string` - Upload path.
- **Returns:** `array` - Upload details (path, url, filename).

#### `publicUpload(string $fieldName, ?string $subFolder = null)`
Handles public file uploads (single or multiple, but returns single if one).

- **Parameters:**
  - `$fieldName`: `string` - Field name.
  - `$subFolder`: `string|null` - Subfolder.
- **Returns:** `array|null` - Upload details or null.

#### `publicMultipleUpload(string $fieldName, ?string $subFolder = null)`
Handles multiple public file uploads.

- **Parameters:**
  - `$fieldName`: `string` - Field name.
  - `$subFolder`: `string|null` - Subfolder.
- **Returns:** `array` - Array of upload details.

#### `saveAuditLog($action, $description = null, array $context = [])`
Saves an audit log entry.

- **Parameters:**
  - `$action`: `string` - Action performed.
  - `$description`: `string|null` - Description.
  - `$context`: `array` - Additional context.
- **Returns:** `void`

#### `extractLevel3WithBreadcrumb(array $items, array $parents = [])`
Extracts level 3 items with breadcrumb labels from a nested array.

- **Parameters:**
  - `$items`: `array` - Nested items.
  - `$parents`: `array` - Parent labels.
- **Returns:** `array` - Extracted fields with breadcrumbs.

### Private Methods

#### `generateDetailSchemaToJson($data)`
Generates and saves the detail schema to a JSON file.

- **Parameters:**
  - `$data`: `array` - Schema data.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `generateColumnsToJson($data)`
Generates and saves the columns configuration to a JSON file.

- **Parameters:**
  - `$data`: `array` - Columns data.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

#### `saveTrash($data)`
Saves deleted data to the trash for potential rollback.

- **Parameters:**
  - `$data`: `mixed` - The data model instance.
- **Returns:** `void`
- **Author:** bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>

## Usage Notes

- Extend this class in your specific controllers to inherit common CRUD and grid functionalities.
- Configure module-specific settings using the setter methods (e.g., `setModule`, `setModel`).
- Ensure proper access control by calling `allowAccessModule` in public methods.
- File uploads are handled via `privateUpload`, `publicUpload`, or `publicMultipleUpload`.
- Audit logs are automatically saved for delete operations.
