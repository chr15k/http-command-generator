# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [v0.2.0] - 2025-06-27
### Changed
- Bump chr15k/php-auth-generator dependency to latest version

## [v0.1.10] - 2025-06-25
### Fixed
- Fix error relating to numeric key values & improve type handling

## [v0.1.9] - 2025-06-24
### Fixed
- Fix error caused by nested arrays in pipeline

### Changed
- Body data can now be provided as multi-value array keys

## [v0.1.8] - 2025-06-23
### Fixed
- Ensure new lines for multiple body options in curl output

## [v0.1.7] - 2025-06-21
### Changed
- update .gitattributes
- update .gitignore

### Added
- add test to ensure params pre-encoded in the URL are not double encoded when merged back in with custom queries

## [v0.1.6] - 2025-06-21
### Fixed
- update composer.json to use latest chr15k/php-auth-generator version

## [v0.1.5] - 2025-06-21
### Changed
- updated readme

## [v0.1.4] - 2025-06-21
### Changed
- updated readme & main.yml

## [v0.1.3] - 2025-06-20
### Fixed
- remove magic method from HttpCommand class for clearer readability and usage

## [v0.1.2] - 2025-06-19
### Fixed
- Passing test suite

## [v0.1.1] - 2025-06-19
### Fixed
- x-www-form-urlencoded form data now encodes by default

## [v0.1.0] - 2025-06-18
### Added
- Adds First Version
- Includes request builder system with fluent API
- Supports GET, POST, PUT, PATCH, DELETE, HEAD, and OPTIONS methods
- Provides header management and manipulation
- Implements request body formatting for JSON and form data
- Features URL parameter handling
- Includes authentication support
