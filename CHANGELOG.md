# Yii2 Locker Extension - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- `README.md` - `Usage` and `Exceptions` blocks
- `src/LockInterface` - lock model interface for custom lock class
- Ability to install custom lock class via definitions

### Changed

- Move part logic from `src/LockManager.php` to `src/Lock.php`

### Fixed
- `Bootstrap` - fix initialization

## 1.0.0-alpha2 - 2017-08-18

### Added
- `Bootstrap` - bootstrapping component as well as an localization initializator.

### Fixed
- `src/LockManager.php` - component init before user authentification.
- `checkAccess` callable in `notamedia\locker\rest\Action`.

## 1.0.0-alpha - 2017-07-28