# kynx/po-parser

[![Continuous Integration](https://github.com/kynx/po-parser/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/kynx/po-parser/actions/workflows/continuous-integration.yml)

This is a modernised fork of [sepia/po-parser]. It parses [`gettext` PO] files (*.po files), enabling you to edit their 
contents using PHP.

## Features

PoParser supports following parsing features:

- header section.
- `msgid`, both single and multiline.
- `msgstr`, both single and multiline.
- `msgctxt` (Message context).
- `msgid_plural` (plurals forms).
- `#`, keys (flags).
- `<span>#` keys (translator comments)
- `#.` keys (Comments extracted from source code).
- `#:` keys (references).
- `#|` keys (previous strings), both single and multiline.
- `#~` keys (old entries), both single and multiline.

## Usage

```php
<?php 
// Parse a po file
$fileHandler = new Sepia\PoParser\SourceHandler\FileSystem('es.po');

$poParser = new Sepia\PoParser\Parser($fileHandler);
$catalog  = $poParser->parse();

// Get an entry
$entry = $catalog->getEntry('welcome.user');

// Update entry
$entry = new Entry('welcome.user', 'Welcome User!');
$catalog->setEntry($entry);

// You can also modify other entry attributes as translator comments, code comments, flags...
$entry->setTranslatorComments(['This is shown whenever a new user registers in the website']);
$entry->setFlags(['fuzzy', 'php-code']);
```

### Save Changes back to a file

Use `PoCompiler` together with `FileSystem` to save a catalog back to a file:

```php
$fileHandler = new Sepia\PoParser\SourceHandler\FileSystem('en.po');
$compiler = new Sepia\PoParser\PoCompiler();
$fileHandler->save($compiler->compile($catalog));
```

## Upgrading from `sepia/po-parser`

The initial release is intended to be a drop-in replacement for `sepia/po-parser`, with property and return types and
modern PHP syntax and development tooling to make maintenance easier. The original library is pretty feature-complete, 
so I don't anticipate major changes. But if there are bugs please open an issue!

[sepia/po-parser]: https://github.com/pherrymason/PHP-po-parser
[`gettext` PO]: https://www.gnu.org/software/gettext/manual/html_node/PO-Files.html
