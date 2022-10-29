# Release Notes

## [Unreleased](https://github.com/surgiie/blade/compare/v0.2.0...master)
## [v0.2.0] - 2022-10-26

### Added

- `@php`,`@component` to the list of directives that are shifted to start of line by @surgiie in https://github.com/surgiie/blade/pulls/1
- `<x-*>` blade component support by @surgiie in https://github.com/surgiie/blade/pulls/1
- `blade()` helper that returns static instance by @surgiie in https://github.com/surgiie/blade/pulls/1
-  `Blade::shouldUseCachedCompiledFiles()` and `Blade::useCachedCompiledFiles()` to determine if files are exired in `FileCompiler@isExpired` by @surgiie in https://github.com/surgiie/blade/pulls/1
### Changed
-  `FileCompiler@isExpired` utilizes new `Blade::shouldUseCachedCompiledFiles()` and `Blade::useCachedCompiledFiles()` to determine if files are exired by @surgiie in https://github.com/surgiie/blade/pulls/1
- `@include` gets a new line implictly added to avoid merging with next line on files @surgiie in https://github.com/surgiie/blade/pulls/1
- `compile` flushes file finder after compile to ensure cached view property doesnt return unexpected file contents from cached file collisions when using relative paths @surgiie in https://github.com/surgiie/blade/pulls/1
- `@endcomponent` gets a new line implictly added to avoid merging with next line on files by @surgiie in https://github.com/surgiie/blade/pulls/1

## [v0.1.0] - 2022-10-20

Initial Release
