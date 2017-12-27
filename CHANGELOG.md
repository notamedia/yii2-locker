# Yii2 Locker Extension - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## 1.0.0-beta - 2017-12-27

### Added

- `README.md` - `Usage` and `Exceptions` blocks.
- `LockInterface` - lock model interface for custom lock class.
- Ability to install custom lock class via definitions.

### Changed

- Move part logic from `LockManager` to `Lock`.

### Fixed
- `Bootstrap` - fix initialization.

## 1.0.0-alpha2 - 2017-08-18

### Added
- `Bootstrap` - bootstrapping component as well as an localization initializator.

### Fixed
- `LockManager` - component init before user authentification.
- `checkAccess` callable in `notamedia\locker\rest\Action`.

## 1.0.0-alpha - 2017-07-28