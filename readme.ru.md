# salesrender/plugin-component-form

Компонент описания форм для экосистемы плагинов SalesRender. Предоставляет иерархическую систему форм (`Form` -> `FieldGroup` -> `FieldDefinition`) с типизированными полями, валидацией, зависимостями между полями, автодополнением и возможностью предпросмотра. Используется каждым плагином SalesRender для объявления форм настроек и пакетной конфигурации.

## Установка

```bash
composer require salesrender/plugin-component-form
```

## Требования

| Требование | Версия |
|---|---|
| PHP | >= 7.4.0 |
| ext-json | * |
| adbario/php-dot-notation | ^2.2 |

## Обзор

Каждый плагин SalesRender предоставляет одну или несколько форм (настройки, накладная, пакетная обработка), которые платформа отображает в административном интерфейсе. Данный компонент описывает формы на стороне PHP -- он не рендерит HTML. Вместо этого все классы реализуют `JsonSerializable`, что позволяет сериализовать их в JSON для потребления фронтендом.

### Архитектура

```
Form
 |-- title, description, button
 |-- FieldGroup[] (по имени группы)
      |-- title, description
      |-- FieldDefinition[] (по имени поля)
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
      |-- dependencies[] (поле -> [зависит от полей])

FormData (наследуется от Adbar\Dot)
 |-- доступ к значениям через dot-нотацию

Система автодополнения (autocomplete)
 |-- AutocompleteInterface
 |-- AutocompleteRegistry (singleton)

Система предпросмотра (preview)
 |-- TablePreviewInterface  / TablePreviewRegistry
 |-- MarkdownPreviewInterface / MarkdownPreviewRegistry
```

## Основные классы

### Form

Контейнер верхнего уровня. Реализует `JsonSerializable`.

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, array $fieldGroups, string $button, array $context = [])` | Создаёт форму; `$fieldGroups` -- ассоциативный массив экземпляров `FieldGroup` |
| `getTitle` | `getTitle(): string` | Заголовок формы, отображаемый в интерфейсе |
| `getDescription` | `getDescription(): ?string` | Необязательное описание (в некоторых плагинах поддерживается markdown) |
| `getGroups` | `getGroups(): FieldGroup[]` | Возвращает все группы полей по имени группы |
| `getButton` | `getButton(): string` | Надпись на кнопке отправки |
| `getDefaultData` | `getDefaultData(): FormData` | Собирает значения по умолчанию из всех описаний полей |
| `clearRedundant` | `clearRedundant(FormData $formData): FormData` | Удаляет ключи данных, отсутствующие в описании формы |
| `validateData` | `validateData(FormData $formData): bool` | Валидирует все значения; возвращает `true`, если все проверки пройдены |
| `getErrors` | `getErrors(FormData $formData): array` | Возвращает вложенный массив ошибок `[groupName][fieldName] => [errors]` |
| `getContext` | `getContext(): array` | Возвращает контекст формы |
| `setContext` | `setContext(array $context): void` | Устанавливает контекст формы |

### FieldGroup

Группирует связанные поля вместе. Реализует `JsonSerializable`.

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, array $fields, array $dependencies = [], array $context = [])` | Создаёт группу; `$fields` -- массив `FieldDefinition[]`, `$dependencies` связывает имена полей с массивами имён полей, от которых они зависят |
| `getTitle` | `getTitle(): string` | Заголовок группы |
| `getDescription` | `getDescription(): ?string` | Необязательное описание группы |
| `getFields` | `getFields(): FieldDefinition[]` | Возвращает все поля по имени |
| `getDependencies` | `getDependencies(): array` | Возвращает карту зависимостей |
| `getContext` | `getContext(): array` | Возвращает контекст группы |
| `setContext` | `setContext(array $context): void` | Устанавливает контекст группы |

### FieldDefinition (abstract)

Базовый класс для всех типов полей. Реализует `JsonSerializable`.

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, callable $validator, $default = null, $context = null)` | Создаёт описание поля с callback-валидатором |
| `getTitle` | `getTitle(): string` | Название поля |
| `getDescription` | `getDescription(): ?string` | Подсказка для поля |
| `validate` | `validate($value, FormData $data): bool` | Возвращает `true`, если валидация пройдена |
| `getErrors` | `getErrors($value, FormData $data): array` | Вызывает валидатор; возвращает массив сообщений об ошибках (пустой = валидно) |
| `getDefault` | `getDefault()` | Возвращает значение по умолчанию |
| `getContext` | `getContext()` | Возвращает контекст поля |
| `getDefinition` | `getDefinition(): string` | *(абстрактный)* Возвращает строку-идентификатор типа |

### Типы полей

| Класс | Строка definition | Наследуется от | Описание |
|---|---|---|---|
| `BooleanDefinition` | `'boolean'` | `FieldDefinition` | Переключатель (checkbox) |
| `IntegerDefinition` | `'integer'` | `FieldDefinition` | Поле ввода целого числа |
| `FloatDefinition` | `'float'` | `FieldDefinition` | Поле ввода числа с плавающей точкой |
| `StringDefinition` | `'string'` | `FieldDefinition` | Однострочное текстовое поле (сериализуется с `multiline: false`) |
| `MultilineStringDefinition` | `'string'` | `StringDefinition` | Многострочное текстовое поле (сериализуется с `multiline: true`) |
| `PasswordDefinition` | `'password'` | `FieldDefinition` | Поле ввода пароля (маскированное) |
| `MarkdownDefinition` | `'markdown'` | `StringDefinition` | Редактор markdown |
| `FileDefinition` | `'file'` | `FieldDefinition` | Поле загрузки файла |
| `IFrameDefinition` | `'iframe'` | `FieldDefinition` | Встраивает iframe; добавляет параметр URL `$iframe` |
| `ListOfEnumDefinition` | `'listOfEnum'` | `FieldDefinition` | Множественный выбор из списка; поддерживает `Limit` и различные источники значений |
| `TablePreviewField` | `'tablePreview'` | `FieldDefinition` | Табличный предпросмотр (только чтение); ссылается на именованный previewer |
| `MarkdownPreviewField` | `'markdownPreview'` | `FieldDefinition` | Предпросмотр markdown (только чтение); ссылается на именованный previewer |

### IFrameDefinition

Расширяет `FieldDefinition`, добавляя URL встроенного iframe.

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, callable $validator, string $iframe, $default = null, $context = null)` | Добавляет параметр `$iframe` -- путь к iframe |
| `getIframe` | `getIframe(): string` | Возвращает URL iframe |

### ListOfEnumDefinition

Поле множественного выбора с настраиваемыми источниками значений и ограничениями выбора.

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, callable $validator, ValuesListInterface $valuesList, ?Limit $limit, $default = null, $context = null)` | Создаёт поле множественного выбора |
| `getValues` | `getValues(): ValuesListInterface` | Возвращает источник значений |
| `getLimit` | `getLimit(): ?Limit` | Возвращает ограничения мин./макс. выбора или `null` |

### TablePreviewField / MarkdownPreviewField

Поля предпросмотра (только чтение). Валидатор всегда `fn() => []` (валидация не требуется).

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(string $title, ?string $description, string $previewer, $default = null, $context = null)` | `$previewer` -- зарегистрированное имя, разрешаемое через соответствующий registry |
| `getPreviewer` | `getPreviewer(): string` | Возвращает имя previewer |

### FormData

Наследуется от `Adbar\Dot` (обёртка над массивом с dot-нотацией). Обеспечивает доступ к значениям формы через точечную нотацию, например `$formData->get('auth.token')`.

### Источники значений ListOfEnum

| Класс | Описание |
|---|---|
| `StaticValues` | Жёстко заданный массив вариантов. Каждый элемент должен содержать ключи `'title'` и `'group'`. |
| `DynamicValues` | Ссылается на именованную конечную точку autocomplete (разрешается через `AutocompleteRegistry`). |
| `CallableValues` | Лениво вычисляет callable, возвращающий массив, совместимый с `StaticValues`. Кэширует результат. |

### Limit

Контролирует минимальное/максимальное количество выбранных значений для `ListOfEnumDefinition`.

| Метод | Сигнатура | Описание |
|---|---|---|
| `__construct` | `__construct(?int $min, ?int $max)` | Оба параметра nullable; `null` означает отсутствие ограничения |
| `getMin` | `getMin(): ?int` | Минимальное количество обязательных выборов |
| `getMax` | `getMax(): ?int` | Максимальное количество допустимых выборов |

### AutocompleteInterface

Реализуйте этот интерфейс для обеспечения серверного автодополнения полей с `DynamicValues`.

| Метод | Сигнатура | Описание |
|---|---|---|
| `query` | `query(string $query, array $dependencies, array $context): array` | Поиск по текстовому запросу; возвращает массив вариантов |
| `values` | `values(array $values, array $dependencies, array $context): array` | Разрешает конкретные значения в отображаемые подписи |
| `validate` | `validate(array $values, array $dependencies, array $context): bool` | Проверяет допустимость выбранных значений |

### AutocompleteRegistry

Singleton-реестр, связывающий имена autocomplete с реализациями.

| Метод | Сигнатура | Описание |
|---|---|---|
| `config` | `static config(callable $resolver): void` | Регистрирует резолвер `function(string $name): ?AutocompleteInterface` |
| `getAutocomplete` | `static getAutocomplete(string $name): ?AutocompleteInterface` | Разрешает autocomplete по имени |

### TablePreviewInterface / MarkdownPreviewInterface

| Метод | Сигнатура | Описание |
|---|---|---|
| `render` | `render(array $dependencies, array $context): array` | Рендерит данные предпросмотра на основе текущих зависимостей и контекста |

### TablePreviewRegistry / MarkdownPreviewRegistry

Singleton-реестры, идентичные по паттерну `AutocompleteRegistry`.

| Метод | Сигнатура | Описание |
|---|---|---|
| `config` | `static config(callable $resolver): void` | Регистрирует резолвер `function(string $name): ?Interface` |
| `getTablePreview` / `getMarkdownPreview` | `static get*(string $name): ?Interface` | Разрешает реализацию предпросмотра по имени |

### ValidatorInterface

Необязательный интерфейс для callback-валидаторов (валидаторы могут быть как обычными замыканиями, так и объектами, реализующими этот интерфейс).

| Метод | Сигнатура | Описание |
|---|---|---|
| `__invoke` | `__invoke($value, FieldDefinition $definition, FormData $data): array` | Возвращает массив строк с ошибками; пустой массив означает успешную валидацию |

### Исключения

| Класс | Описание |
|---|---|
| `AutocompleteRegistryException` | Выбрасывается при вызове `AutocompleteRegistry::getAutocomplete()` до `config()` |
| `TablePreviewRegistryException` | Выбрасывается при вызове `TablePreviewRegistry::getTablePreview()` до `config()` |
| `MarkdownPreviewRegistryException` | Выбрасывается при вызове `MarkdownPreviewRegistry::getMarkdownPreview()` до `config()` |
| `InvalidDependencyException` | Выбрасывается, когда зависимость в `FieldGroup` ссылается на несуществующее имя поля |

## Примеры использования

Все приведённые ниже примеры взяты из реальных production-плагинов.

### Простая форма настроек со строковыми полями и полем пароля

Из `plugin-pbx-sipsim/src/Forms/SettingsForm.php`:

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

### Форма с несколькими типами полей, ListOfEnum и зависимостями

Из `plugin-logistic-bluedart/src/Form/SettingsForm.php`:

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
                // 'codFixedValue' отображается только при изменении 'codFixed'
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

### DynamicValues с автодополнением

Из `plugin-logistic-cdek/src/Waybill/WaybillForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Limit;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values\DynamicValues;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnumDefinition;

$cityField = new ListOfEnumDefinition(
    'Sender city',
    null,
    $nonEmpty,
    new DynamicValues('citiesFrom'),  // разрешается через AutocompleteRegistry
    new Limit(1, 1),
    $defaultSenderCity
);
```

### Регистрация autocomplete в bootstrap

Из `plugin-logistic-bluedart/bootstrap.php`:

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

### Реализация AutocompleteInterface

Из `plugin-logistic-shipox/src/Autocomplete/CityAutocomplete.php`:

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
        // вызов внешнего API, возврат ['value_key' => ['title' => '...', 'group' => '...']]
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

### MarkdownPreviewField с зависимостями

Из `plugin-logistic-cdek/src/Waybill/WaybillForm.php`:

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
            'delivery_data_preview',  // разрешается через MarkdownPreviewRegistry
            null,
            $context
        ),
    ],
    [
        'deliveryData' => ['tariff', 'citySender', 'weight'],
    ]
);
```

### Регистрация MarkdownPreviewRegistry в bootstrap

Из `plugin-logistic-bluedart/bootstrap.php`:

```php
use SalesRender\Plugin\Components\Form\MarkdownPreview\MarkdownPreviewRegistry;

MarkdownPreviewRegistry::config(function () {
    return new DeliveryInfoMarkdownPreview();
});
```

### TablePreviewField

Из `plugin-macros-example/src/Forms/SettingsForm.php`:

```php
use SalesRender\Plugin\Components\Form\FieldDefinitions\TablePreviewField;

$field = new TablePreviewField(
    'Table preview',
    'Preview description',
    'example',       // имя previewer, разрешается через TablePreviewRegistry
    ['default' => 'text'],
    $context
);
```

### IFrameDefinition

Из `plugin-macros-example/src/Forms/SettingsForm.php`:

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
    '5'  // значение по умолчанию
);
```

### Валидация и чтение данных формы

```php
$form = new Form(/* ... */);
$formData = new FormData($submittedArray);

if ($form->validateData($formData)) {
    $token = $formData->get('auth.token');       // доступ через dot-нотацию
    $length = $formData->get('package.length');
} else {
    $errors = $form->getErrors($formData);
    // $errors['auth']['token'] => ['Field cannot be empty']
}
```

### Получение значений по умолчанию

```php
$form = new Form(/* ... */);
$defaults = $form->getDefaultData();
// $defaults->get('package.length') => 10
```

## Конфигурация

### Autocomplete

Если какая-либо форма использует `DynamicValues`, необходимо сконфигурировать `AutocompleteRegistry` в `bootstrap.php`:

```php
AutocompleteRegistry::config(function (string $name): ?AutocompleteInterface {
    // верните соответствующую реализацию или null
});
```

### Table Preview

Если какая-либо форма использует `TablePreviewField`, настройте `TablePreviewRegistry`:

```php
TablePreviewRegistry::config(function (string $name): ?TablePreviewInterface {
    // верните соответствующую реализацию или null
});
```

### Markdown Preview

Если какая-либо форма использует `MarkdownPreviewField`, настройте `MarkdownPreviewRegistry`:

```php
MarkdownPreviewRegistry::config(function (string $name): ?MarkdownPreviewInterface {
    // верните соответствующую реализацию или null
});
```

## Справочник API

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

### Типы полей

| Строка definition | Класс | JSON `multiline` |
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

### Сериализация в JSON

Все классы реализуют `JsonSerializable`. Форма сериализуется в следующий формат:

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

## Зависимости

| Пакет | Версия | Назначение |
|---|---|---|
| `adbario/php-dot-notation` | ^2.2 | Доступ к массиву через dot-нотацию для `FormData` (наследуется от `Adbar\Dot`) |

## Смотрите также

- [salesrender/plugin-component-settings](https://github.com/SalesRender/plugin-component-settings) -- сохраняет `FormData`, отправленные через формы; использует `Settings::setForm()` для привязки формы
- [salesrender/plugin-component-purpose](https://github.com/SalesRender/plugin-component-purpose) -- классификация назначения плагинов (класс + сущность)
- [salesrender/plugin-component-info](https://github.com/SalesRender/plugin-component-info) -- метаданные плагина; объединяется с формой и назначением при инициализации в bootstrap
