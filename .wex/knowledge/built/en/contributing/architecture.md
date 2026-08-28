## Architecture

The library is a flat collection of static utility classes, abstract base types, and composable traits. There is no service container, no registry, and no boot sequence: every public method is callable directly on the class. The single runtime dependency is `symfony/string`, used by `TextHelper` for Unicode-aware case conversions and slugging.

The autoloader maps the root namespace `Wexample\Helpers\` to src:

```json
"autoload": {
    "psr-4": {
        "Wexample\\Helpers\\": "src/"
    }
}
```

### `src/Helper/` — static utility classes

Each class in src/Helper is a pure static class with no constructor and no shared state. Callers reach them directly:

```php
TextHelper::toSnake('MyClassName');      // my_class_name
ArrayHelper::canonicalize($data);
ClassHelper::getShortName(MyEntity::class);
```

The helpers and what they own:

| Class | Responsibility |
|---|---|
| `ArrayHelper` | Structural comparison (`areSame`, `normalize`), canonicalization, dot-path access (`getItemByPath`), flattening, item removal. |
| `ClassHelper` | Namespace and class-name math: `getShortName`, `getRealClassPath` (Doctrine proxy-aware), `getClassCousin`, attribute scanning via `ReflectionClass` / `ReflectionMethod`, getter/setter builders and callers. |
| `DateHelper` | PHP `date()` format string constants only — no logic. |
| `DirHelper` | `createDirRecursive`, `removeDirRecursive`, `listFiles` with a glob pattern. |
| `FileHelper` | File extension and path separator constants. `putContentsRecursive` (delegates directory creation to `DirHelper`). `scanDirectoryForFiles` with a `RecursiveDirectoryIterator`. |
| `HttpHelper` | HTTP `Content-Type` string constants only — no logic. |
| `LoremIpsumHelper` | `generate(int $length)` — assembles placeholder text from a fixed sentence pool, always starting with `"Lorem ipsum"`. |
| `PathHelper` | Filesystem path operations: `relativeTo`, `join`, `getPathParts` (slice after an offset), `getCousin` (maps a path under one base to the equivalent path under another). |
| `PlaceholderHelper` | Demo URL and email constants for use in fixtures and tests. |
| `TextHelper` | The largest class. Covers case conversions (`toSnake`, `toKebab`, `toCamel`, `toClass`), slugging (`slugify` via `AsciiSlugger`), prefix/suffix removal, chunk splitting, boolean and float parsing, HTML-to-text, ASCII colour wrapping, binary encoding, AES-256-CBC encrypt/decrypt, and secure random ID generation. |
| `VariableSpecialHelper` | A single `EMPTY_STRING = ''` constant. |

#### Dependency edges between helpers

`ClassHelper` calls `TextHelper` for case conversions and `PathHelper::getCousin` for `getClassCousin`. `TextHelper` references `ClassHelper::getFieldGetterValue` in `objectToFileName` and `FileHelper::EXTENSION_SEPARATOR` in `trimExtension`. `FileHelper::putContentsRecursive` calls `DirHelper::createDirRecursive`. These are the only cross-helper calls; everything else is self-contained.

### `src/Class/` — abstract base type and class-reflection traits

src/Class/AbstractFromArrayObject.php is an abstract base for value objects built from arrays or plain objects. The constructor calls the abstract `getAllowedProperties()` on the concrete subclass and copies only the declared keys:

```php
foreach (static::getAllowedProperties() as $property) {
    if (isset($data[$property])) {
        $this->$property = $data[$property];
    }
}
```

src/Class/Traits holds three traits that attach class-name introspection to any class:

- `HasShortClassNameClassTrait` — adds `getShortClassName()`, which calls `ClassHelper::getShortName(static::class)` and strips an optional suffix returned by the overridable `getClassNameSuffix()`.
- `HasSnakeShortClassNameClassTrait` — composes `HasShortClassNameClassTrait` and adds `getSnakeShortClassName()` by piping the result through `TextHelper::toSnake()`.
- `HasUniqueId` — adds a `$uniqueId` string property with `getUniqueId()`, `setUniqueId()`, and `generateUniqueId(string $prefix)`, which writes 16 hex characters from `random_bytes(8)`.

### `src/Traits/` — general-purpose traits

src/Traits/WithDomId.php attaches a `$domId` string property with `getDomId()` and `setDomId()`. It has no coupling to any helper class.

### `src/Testing/` — PHPUnit assertion traits

Both traits in src/Testing/Traits are mixed into PHPUnit test cases; neither ships test cases of its own.

`WithArrayTestCase` provides `assertArraysEqual(array $expected, array $actual, string $message, bool $allowEmptyMissing)`. It calls `ArrayHelper::areSame()` to decide whether a failure exists, then `ArrayHelper::normalize()` to align empty-array divergences before generating a unified diff with `SebastianBergmann\Diff\Differ`.

`WithYamlTestCase` composes `WithArrayTestCase` and adds `assertYamlFilesEqual()`, which parses both files with `Symfony\Component\Yaml\Yaml::parse` and delegates to `assertArraysEqual`.

### Call path example

A call to `HasSnakeShortClassNameClassTrait::getSnakeShortClassName()` on a concrete class traverses:

1. `HasSnakeShortClassNameClassTrait::getSnakeShortClassName()` in src/Class/Traits/HasSnakeShortClassNameClassTrait.php
2. → `HasShortClassNameClassTrait::getShortClassName()` in src/Class/Traits/HasShortClassNameClassTrait.php
3. → `ClassHelper::getShortName(static::class)` in src/Helper/ClassHelper.php — resolves the Doctrine proxy if needed, then returns `ReflectionClass::getShortName()` or falls back to `TextHelper::getLastChunk()`
4. → back in `HasShortClassNameClassTrait`, optional suffix stripped via `TextHelper::removeSuffix()`
5. → `TextHelper::toSnake()` in src/Helper/TextHelper.php — delegates to `(new UnicodeString($string))->snake()`
