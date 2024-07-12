# Craft Seeder Changelog

## 5.0.0.3 - 2024-07-12

### added

- enhance unique field handling
- added an event to register unique fields

## 5.0.0.2 - 2024-07-10

### added

- added a new bulk-populate option from ElementIndex-Pages

## 5.0.0.1 - 2024-07-08

### added 

- add a way to populate certain fields for single elements directly via CP or console

## 5.0.0 - 2024-06-26

### added

- added lorem-pixel to seed images
- added entry type select for entries 

## 5.0.0-RC10 - 2024-06-17

### changed

- only show seeder in CP for entries

## 5.0.0-RC9 - 2024-06-14

### fixed

- fixed a bug when an element type had no field layout

## 5.0.0-RC8 - 2024-06-11

### added

- added Either Seo as supported fields
- added tablemaker as supported fields

## 5.0.0-RC2 - 2024-04-04

### fixed

- fixed Craft 5 issues, remove all Matrix hints, seed Entries instead of Matrix blocks

## 5.0.0-RC1 - 2024-04-04

### changed

- initial Craft 5 version

## 4.0.0-RC4 - 2023-06-29

### changed

- refactor plugin code

### added 

- add a function to seed matrix fields

## 4.0.0-RC1 - 2023-06-29

### changed

- first initial craft 4 draft

## 3.1.0 - Unreleased
### Added
- Added `eachMatrixBlock` option, to seed a matrix with one of each blocktype in a random order ([#13](https://github.com/studioespresso/craft3-seeder/issues/13))
- Added`useLocalAssets` option, to seed asset fields with assets from a specified volume & folder, to be used in case you have your own set of test images.
- Added support for [rias/craft-position-fieldtype](https://github.com/Rias500/craft-position-fieldtype)
## 3.0.1 - 2019-04-09

### Added
- Progress bars when generating elements ([#4](https://github.com/studioespresso/craft3-seeder/issues/4))

### Fixed
- Fixed an issue with seeding categories ([#8](https://github.com/studioespresso/craft3-seeder/issues/8))
- Fixed an issue with seeding entries for sections without fields

## 3.0.0 - 2019-02-05

### Added
- Seeder now works with Craft 3.1 🎉
- Added support for [statikbe/craft-cta-field](https://github.com/statikbe/craft3-ctafield)
### Fixed
- Fixed asset fields in Craft 3.1
- Fixed an issue where seeding a Supertable field in a Matrix field would crash
- Fixed an issue with minimum & maximum number of blocks on a Supertable field

## 2.1.0 - 2018-09-19

### Added
- Added support for fields on Users
- Added support for fields on Categories

## 2.0.0 - 2018-08-24
### Changed
- The commands now take named parameters in stead of just IDs
- The commands now also work with section/group handle or with section/group id
### Added
- Supertable support, Super table static field support and all core fields in a Supertable field

## 1.0.3 - 2018-05-29
### Fixed
- Fixes an issues with min/max rows in table fields (issue #3)

## 1.0.2 - 2018-05-24
### Fixed
- Fixed an issue with asset fields that did not have a limit set. Now we'll seed a random number of images between 1 and 5 for those.

## 1.0.1 - 2018-05-23
### Changed
- Seeded images are now smaller (1600x1200), which are served more reliable from lorempixel.com

## 1.0.0 - 2018-05-16
### Added
- Initial release
