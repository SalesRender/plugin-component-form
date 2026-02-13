# salesrender/plugin-component-form

Form definition component for the SalesRender plugin ecosystem. Provides a hierarchical form system (`Form` -> `FieldGroup` -> `FieldDefinition`) with typed fields, validation, dependencies between fields, autocomplete, and preview capabilities. Used by every SalesRender plugin to declare its settings and batch configuration forms.

## Installation

```bash
composer require salesrender/plugin-component-form
```

## Requirements

| Requirement | Version |
|---|---|
| PHP | >= 7.4.0 |
| ext-json | * |
| adbario/php-dot-notation | ^2.2 |

## Overview

Every SalesRender plugin exposes one or more forms (settings, waybill, batch) that the platform renders in the admin UI. This component provides the PHP-side form definition -- it does not render HTML. Instead, all classes implement `JsonSerializable` so they can be serialized to JSON and consumed by the frontend.

### Architecture

```
Form
 |-- title, description, button
 |-- FieldGroup[] (keyed by group name)
      |-- title, description
      |-- FieldDefinition[] (keyed by field name)
      |    |-- BooleanDefinition
      |    |-- IntegerDefinition
      |    |-- FloatDefinition
      |    |-- StringDefinition
      |    |-- MultilineStringDefinition
      |    |-- PasswordDefinition
      |    |-- MarkdownDefinition
      |    |-- FileDefinition
      |    |-- IFrameDefinition
      |    |-- ListOfEnumDefinition
      |    |-- TablePreviewField
      |    |-- MarkdownPreviewField
      |-- dependencies[] (field -> [depends-on fields])

FormData (extends Adbar\Dot)
 |-- dot-notation access to submitted values

Autocomplete system
 |-- AutocompleteInterface
 |-- AutocompleteRegistry (singleton)

Preview system
 |-- TablePreviewInterface  / TablePreviewRegistry
 |-- MarkdownPreviewInterface / MarkdownPreviewRegistry
```

## Key Classes

### Form

Top-level form container. Implements `JsonSerializable`.

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, array $fieldGroups, string $button, array $context = [])` | Creates a form; `$fieldGroups` is an associative array of `FieldGroup` instances |
| `getTitle` | `getTitle(): string` | Form title displayed in the UI |
| `getDescription` | `getDescription(): ?string` | Optional description (supports markdown in some plugins) |
| `getGroups` | `getGroups(): FieldGroup[]` | Returns all field groups, keyed by group name |
| `getButton` | `getButton(): string` | Label for the submit button |
| `getDefaultData` | `getDefaultData(): FormData` | Collects default values from all field definitions |
| `clearRedundant` | `clearRedundant(FormData $formData): FormData` | Removes data keys that do not exist in the form definition |
| `validateData` | `validateData(FormData $formData): bool` | Validates all field values; returns `true` if all pass |
| `getErrors` | `getErrors(FormData $formData): array` | Returns a nested array of errors `[groupName][fieldName] => [errors]` |
| `getContext` | `getContext(): array` | Returns the form context |
| `setContext` | `setContext(array $context): void` | Sets the form context |

### FieldGroup

Groups related fields together. Implements `JsonSerializable`.

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, array $fields, array $dependencies = [], array $context = [])` | Creates a group; `$fields` is `FieldDefinition[]`, `$dependencies` maps field names to arrays of field names they depend on |
| `getTitle` | `getTitle(): string` | Group title |
| `getDescription` | `getDescription(): ?string` | Optional group description |
| `getFields` | `getFields(): FieldDefinition[]` | Returns all fields, keyed by field name |
| `getDependencies` | `getDependencies(): array` | Returns the dependency map |
| `getContext` | `getContext(): array` | Returns the group context |
| `setContext` | `setContext(array $context): void` | Sets the group context |

### FieldDefinition (abstract)

Base class for all field types. Implements `JsonSerializable`.

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, callable $validator, $default = null, $context = null)` | Creates a field definition with a validator callback |
| `getTitle` | `getTitle(): string` | Field label |
| `getDescription` | `getDescription(): ?string` | Help text for the field |
| `validate` | `validate($value, FormData $data): bool` | Returns `true` if validation passes |
| `getErrors` | `getErrors($value, FormData $data): array` | Invokes the validator; returns an array of error messages (empty = valid) |
| `getDefault` | `getDefault()` | Returns the default value |
| `getContext` | `getContext()` | Returns field-level context |
| `getDefinition` | `getDefinition(): string` | *(abstract)* Returns the type identifier string |

### Field Type Definitions

| Class | Definition String | Inherits From | Description |
|---|---|---|---|
| `BooleanDefinition` | `'boolean'` | `FieldDefinition` | Checkbox / toggle |
| `IntegerDefinition` | `'integer'` | `FieldDefinition` | Integer number input |
| `FloatDefinition` | `'float'` | `FieldDefinition` | Floating-point number input |
| `StringDefinition` | `'string'` | `FieldDefinition` | Single-line text input (serializes `multiline: false`) |
| `MultilineStringDefinition` | `'string'` | `StringDefinition` | Multi-line text area (serializes `multiline: true`) |
| `PasswordDefinition` | `'password'` | `FieldDefinition` | Password input (masked) |
| `MarkdownDefinition` | `'markdown'` | `StringDefinition` | Markdown editor |
| `FileDefinition` | `'file'` | `FieldDefinition` | File upload field |
| `IFrameDefinition` | `'iframe'` | `FieldDefinition` | Embeds an iframe; adds `$iframe` URL parameter |
| `ListOfEnumDefinition` | `'listOfEnum'` | `FieldDefinition` | Multi-select from a list of options; supports `Limit` and various value sources |
| `TablePreviewField` | `'tablePreview'` | `FieldDefinition` | Read-only table preview; references a named previewer |
| `MarkdownPreviewField` | `'markdownPreview'` | `FieldDefinition` | Read-only markdown preview; references a named previewer |

### IFrameDefinition

Extends `FieldDefinition` with an embedded iframe URL.

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, callable $validator, string $iframe, $default = null, $context = null)` | Adds an `$iframe` path parameter |
| `getIframe` | `getIframe(): string` | Returns the iframe URL |

### ListOfEnumDefinition

Multi-select field with configurable value sources and selection limits.

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, callable $validator, ValuesListInterface $valuesList, ?Limit $limit, $default = null, $context = null)` | Creates a list-of-enum field |
| `getValues` | `getValues(): ValuesListInterface` | Returns the values source |
| `getLimit` | `getLimit(): ?Limit` | Returns min/max selection limits, or `null` |

### TablePreviewField / MarkdownPreviewField

Read-only preview fields. The validator is always `fn() => []` (no validation needed).

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, string $previewer, $default = null, $context = null)` | `$previewer` is a registered name resolved via the corresponding registry |
| `getPreviewer` | `getPreviewer(): string` | Returns the previewer name |

### FormData

Extends `Adbar\Dot` (dot-notation array wrapper). Provides dot-path access to submitted form values, e.g. `$formData->get('auth.token')`.

### ListOfEnum Value Sources

| Class | Description |
|---|---|
| `StaticValues` | Hardcoded array of options. Each item must have `'title'` and `'group'` keys. |
| `DynamicValues` | References a named autocomplete endpoint (resolved by `AutocompleteRegistry`). |
| `CallableValues` | Lazily evaluates a callable that returns a `StaticValues`-compatible array. Caches the result. |

### Limit

Controls min/max selection count for `ListOfEnumDefinition`.

| Method | Signature | Description |
|---|---|---|
| `__construct` | `__construct(?int $min, ?int $max)` | Both are nullable; `null` means no constraint |
| `getMin` | `getMin(): ?int` | Minimum number of required selections |
| `getMax` | `getMax(): ?int` | Maximum number of allowed selections |

### AutocompleteInterface

Implement this to provide server-side autocomplete for `DynamicValues` fields.

| Method | Signature | Description |
|---|---|---|
| `query` | `query(string $query, array $dependencies, array $context): array` | Search by text query; return options array |
| `values` | `values(array $values, array $dependencies, array $context): array` | Resolve specific values to display labels |
| `validate` | `validate(array $values, array $dependencies, array $context): bool` | Validate that selected values are legitimate |

### AutocompleteRegistry

Singleton registry that maps autocomplete names to implementations.

| Method | Signature | Description |
|---|---|---|
| `config` | `static config(callable $resolver): void` | Register a resolver `function(string $name): ?AutocompleteInterface` |
| `getAutocomplete` | `static getAutocomplete(string $name): ?AutocompleteInterface` | Resolve an autocomplete by name |

### TablePreviewInterface / MarkdownPreviewInterface

| Method | Signature | Description |
|---|---|---|
| `render` | `render(array $dependencies, array $context): array` | Render preview data given current field dependencies and context |

### TablePreviewRegistry / MarkdownPreviewRegistry

Singleton registries, identical pattern to `AutocompleteRegistry`.

| Method | Signature | Description |
|---|---|---|
| `config` | `static config(callable $resolver): void` | Register a resolver `function(string $name): ?Interface` |
| `getTablePreview` / `getMarkdownPreview` | `static get*(string $name): ?Interface` | Resolve a preview implementation by name |

### ValidatorInterface

Optional interface for validator callables (validators can be plain closures or objects implementing this interface).

| Method | Signature | Description |
|---|---|---|
| `__invoke` | `__invoke($value, FieldDefinition $definition, FormData $data): array` | Returns an array of error strings; empty array means valid |

### Exceptions

| Class | Description |
|---|---|
| `AutocompleteRegistryException` | Thrown when `AutocompleteRegistry::getAutocomplete()` is called before `config()` |
| `TablePreviewRegistryException` | Thrown when `TablePreviewRegistry::getTablePreview()` is called before `config()` |
| `MarkdownPreviewRegistryException` | Thrown when `MarkdownPreviewRegistry::getMarkdownPreview()` is called before `config()` |
| `InvalidDependencyException` | Thrown when a `FieldGroup` dependency references a non-existent field name |

## Usage Examples

All examples below are taken from real production plugins.

### Basic settings form with string and password fields

From `plugin-pbx-sipsim/src/Forms/SettingsForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\PasswordDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\StringDefinition;
use SalesRender\Plugin\Components\Form\FieldGroup;
use SalesRender\Plugin\Components\Form\Form;
use SalesRender\Plugin\Components\Form\FormData;

$nonEmpty = function ($value, FieldDefinition $definition, FormData $data) {
    $errors = [];
    if (empty($value)) {
        $errors[] = 'Field cannot be empty';
    }
    return $errors;
};

$form = new Form(
    'Settings',
    'Configure your API connection',
    [
        'main' => new FieldGroup(
            'Main',
            null,
            [
                'url' => new StringDefinition('API URL', 'Provided by support', $nonEmpty),
                'token' => new PasswordDefinition('Token', 'Generated in your dashboard', $nonEmpty),
            ]
        ),
    ],
    'Save'
);
```

### Form with multiple field types, ListOfEnum, and dependencies

From `plugin-logistic-bluedart/src/Form/SettingsForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\BooleanDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\FloatDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\IntegerDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Limit;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values\StaticValues;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnumDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\PasswordDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\StringDefinition;
use SalesRender\Plugin\Components\Form\FieldGroup;
use SalesRender\Plugin\Components\Form\Form;

$form = new Form(
    'Settings',
    'Blue Dart Configuration',
    [
        'auth' => new FieldGroup(
            'Authorization',
            null,
            [
                'client_id' => new StringDefinition('Client ID', null, $nonEmpty),
                'client_secret' => new PasswordDefinition('Client Secret', null, $nonEmpty),
            ]
        ),
        'shipment' => new FieldGroup(
            'Shipment Defaults',
            null,
            [
                'productType' => new ListOfEnumDefinition(
                    'Product Type',
                    null,
                    $nonEmptyEnum,
                    new StaticValues([
                        '0' => ['title' => 'Documents', 'group' => 'Type'],
                        '1' => ['title' => 'Non-Documents / Cargo', 'group' => 'Type'],
                    ]),
                    new Limit(1, 1),
                    ['1']
                ),
                'codFixed' => new BooleanDefinition(
                    'Use fixed COD amount',
                    'If disabled, uses order total',
                    $noValidation,
                    false
                ),
                'codFixedValue' => new FloatDefinition(
                    'Fixed COD value',
                    'Used for all shipments when fixed amount is enabled',
                    $dependedCod,
                    0.0
                ),
            ],
            [
                // 'codFixedValue' is only visible when 'codFixed' changes
                'codFixedValue' => ['codFixed'],
            ]
        ),
        'package' => new FieldGroup(
            'Default Package',
            null,
            [
                'length' => new IntegerDefinition('Length', null, $noValidation, 10),
                'width' => new IntegerDefinition('Width', null, $noValidation, 10),
                'height' => new IntegerDefinition('Height', null, $noValidation, 10),
                'weight' => new FloatDefinition('Weight, kg', null, $noValidation, 0.5),
            ]
        ),
    ],
    'Save'
);
```

### DynamicValues with autocomplete

From `plugin-logistic-cdek/src/Waybill/WaybillForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Limit;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values\DynamicValues;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnumDefinition;

$cityField = new ListOfEnumDefinition(
    'Sender city',
    null,
    $nonEmpty,
    new DynamicValues('citiesFrom'),  // resolved via AutocompleteRegistry
    new Limit(1, 1),
    $defaultSenderCity
);
```

### Registering autocomplete in bootstrap

From `plugin-logistic-bluedart/bootstrap.php`:

```php
use SalesRender\Plugin\Components\Form\Autocomplete\AutocompleteRegistry;

AutocompleteRegistry::config(function (string $name) {
    switch ($name) {
        case 'rates':
            return new RatesAutocomplete();
        default:
            return null;
    }
});
```

### Implementing AutocompleteInterface

From `plugin-logistic-shipox/src/Autocomplete/CityAutocomplete.php`:

```php
use SalesRender\Plugin\Components\Form\Autocomplete\AutocompleteInterface;

final class CityAutocomplete implements AutocompleteInterface
{
    public const NAME = 'city';

    public function query(string $query, array $dependencies, array $context): array
    {
        if (empty(trim($query))) {
            return [];
        }
        // call external API, return ['value_key' => ['title' => '...', 'group' => '...']]
        return $this->getCitiesByCondition($query);
    }

    public function values(array $values, array $dependencies, array $context): array
    {
        return $this->getCitiesByCondition(json_decode($values[0], true)[1]);
    }

    public function validate(array $values, array $dependencies, array $context): bool
    {
        $cities = $this->getCitiesByCondition(json_decode($values[0], true)[1]);
        return array_key_exists(array_shift($values), $cities);
    }
}
```

### MarkdownPreviewField with dependencies

From `plugin-logistic-cdek/src/Waybill/WaybillForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\MarkdownPreviewField;

$group = new FieldGroup(
    'Delivery',
    null,
    [
        'tariff' => new ListOfEnumDefinition(/* ... */),
        'citySender' => new ListOfEnumDefinition(/* ... */),
        'weight' => new FloatDefinition(/* ... */),
        'deliveryData' => new MarkdownPreviewField(
            'Delivery data',
            null,
            'delivery_data_preview',  // resolved via MarkdownPreviewRegistry
            null,
            $context
        ),
    ],
    [
        'deliveryData' => ['tariff', 'citySender', 'weight'],
    ]
);
```

### Registering MarkdownPreviewRegistry in bootstrap

From `plugin-logistic-bluedart/bootstrap.php`:

```php
use SalesRender\Plugin\Components\Form\MarkdownPreview\MarkdownPreviewRegistry;

MarkdownPreviewRegistry::config(function () {
    return new DeliveryInfoMarkdownPreview();
});
```

### TablePreviewField

From `plugin-macros-example/src/Forms/SettingsForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\TablePreviewField;

$field = new TablePreviewField(
    'Table preview',
    'Preview description',
    'example',       // previewer name, resolved via TablePreviewRegistry
    ['default' => 'text'],
    $context
);
```

### IFrameDefinition

From `plugin-macros-example/src/Forms/SettingsForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\IFrameDefinition;

$field = new IFrameDefinition(
    'IFrame field',
    'Description',
    function ($value) {
        if ($value < 0 || $value > 10) {
            return ['Value must be between 0 and 10'];
        }
        return [];
    },
    'iframe/example.html',
    '5'  // default value
);
```

### Validating and reading form data

```php
$form = new Form(/* ... */);
$formData = new FormData($submittedArray);

if ($form->validateData($formData)) {
    $token = $formData->get('auth.token');       // dot-notation access
    $length = $formData->get('package.length');
} else {
    $errors = $form->getErrors($formData);
    // $errors['auth']['token'] => ['Field cannot be empty']
}
```

### Getting default data

```php
$form = new Form(/* ... */);
$defaults = $form->getDefaultData();
// $defaults->get('package.length') => 10
```

## Configuration

### Autocomplete

If any form uses `DynamicValues`, the `AutocompleteRegistry` must be configured in `bootstrap.php`:

```php
AutocompleteRegistry::config(function (string $name): ?AutocompleteInterface {
    // return the appropriate implementation or null
});
```

### Table Preview

If any form uses `TablePreviewField`, configure `TablePreviewRegistry`:

```php
TablePreviewRegistry::config(function (string $name): ?TablePreviewInterface {
    // return the appropriate implementation or null
});
```

### Markdown Preview

If any form uses `MarkdownPreviewField`, configure `MarkdownPreviewRegistry`:

```php
MarkdownPreviewRegistry::config(function (string $name): ?MarkdownPreviewInterface {
    // return the appropriate implementation or null
});
```

## API Reference

### Namespace

```
SalesRender\Plugin\Components\Form
SalesRender\Plugin\Components\Form\FieldDefinitions
SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum
SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values
SalesRender\Plugin\Components\Form\Autocomplete
SalesRender\Plugin\Components\Form\Components
SalesRender\Plugin\Components\Form\TableView
SalesRender\Plugin\Components\Form\MarkdownPreview
SalesRender\Plugin\Components\Form\Exceptions
```

### Field Definition Types

| Definition String | Class | JSON `multiline` |
|---|---|---|
| `'boolean'` | `BooleanDefinition` | -- |
| `'integer'` | `IntegerDefinition` | -- |
| `'float'` | `FloatDefinition` | -- |
| `'string'` | `StringDefinition` | `false` |
| `'string'` | `MultilineStringDefinition` | `true` |
| `'password'` | `PasswordDefinition` | -- |
| `'markdown'` | `MarkdownDefinition` | -- |
| `'file'` | `FileDefinition` | -- |
| `'iframe'` | `IFrameDefinition` | -- |
| `'listOfEnum'` | `ListOfEnumDefinition` | -- |
| `'tablePreview'` | `TablePreviewField` | -- |
| `'markdownPreview'` | `MarkdownPreviewField` | -- |

### JSON Serialization

Every class implements `JsonSerializable`. A form serializes to:

```json
{
    "title": "Settings",
    "description": "Configure plugin",
    "groups": {
        "auth": {
            "title": "Authorization",
            "description": null,
            "fields": {
                "token": {
                    "title": "API Token",
                    "description": "Your API token",
                    "definition": "password",
                    "default": null,
                    "context": null
                }
            },
            "dependencies": {}
        }
    },
    "button": "Save"
}
```

## Dependencies

| Package | Version | Purpose |
|---|---|---|
| `adbario/php-dot-notation` | ^2.2 | Dot-notation array access for `FormData` (extends `Adbar\Dot`) |

## See Also

- [salesrender/plugin-component-settings](https://github.com/SalesRender/plugin-component-settings) -- persists `FormData` submitted via forms; uses `Settings::setForm()` to bind a form
- [salesrender/plugin-component-purpose](https://github.com/SalesRender/plugin-component-purpose) -- plugin purpose classification (class + entity)
- [salesrender/plugin-component-info](https://github.com/SalesRender/plugin-component-info) -- plugin metadata; combined with form and purpose during bootstrap
