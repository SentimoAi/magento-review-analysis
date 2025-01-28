# Changelog

All notable changes to the Sentimo Review Analysis Module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.0] - 2025-01-27
### Changed
- Change package name to `sentimo/magento-review-analysis`.

## [2.1.0] - 2025-01-26
### Added
- Add rating to the Product data model.

### Fixed
- Fix price type in the Product data model.
- Get reviews in batches to avoid too long requests.
## [2.0.0] - 2023-04-30
### Added
- Implementation of sentimo/php-client library for API requests.

### Removed
- Custom API request implementation.

## [1.1.1] - 2023-04-15
### Changed
- Improved README.md with clearer instructions and formatting.

## [1.1.0] - 2023-04-12
### Added
- Link to moderated reviews in Sentimo Admin from Magento Admin.

## [1.0.0] - 2023-04-04
### Added
- Initial release of the Sentimo Review Analysis Module.
- Integration with Sentimo API for sentiment analysis of customer reviews.
- Configuration options in Magento Admin for API connection settings including base URI and API key.
- Configuration options for specifying the review sync frequency and date range.
- Automatic update of review statuses based on sentiment analysis results.

### Changed

### Deprecated

### Removed

### Fixed

### Security
