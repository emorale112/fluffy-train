# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## [2.0.0] - 06-11-2020

### Added

- Remove Guzzle dependency to avoid conflict with other versions used in out customer website

### Changed
- On ride creation, do not need to send params in "ride" key of array. Our library will handle it, you juste need to pass ride params array("discription" => ....)

[2.0.0.]: https://github.com/Cocolis-1/cocolis-php/tree/2.0.0