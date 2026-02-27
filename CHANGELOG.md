# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.10] - 2026-02-27

### Added
- `implode:` directive to implode array values

## [1.0.9] - 2026-02-27

### Fixed
- Fixed bug where nested dot notation wasn't working on block view.

## [1.0.8] - 2026-02-27

### Added
- Initial release of `oneawebmarketing/console-awesome-table`
- `HasAwesomeTable` trait with `awesomeTable()` method for automatic table/block rendering
- Auto-detection of table headings from row keys when no headings are provided
- Automatic fallback to `dataBlock()` when table width exceeds terminal width
- `dataBlock()` method to display rows as framed key/value blocks
- `title()` method for rendering bordered title banners
- Display of undisplayed/unused field names via `infoUnusedKeys()`
- Support for dot-notation data retrieval via Laravel's `data_get()` helper
- Sorted listing of undisplayed keys

### Fixed
- Fixed error where `$undisplayedKeys` was undefined when headings were auto-detected
