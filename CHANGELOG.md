# Release Notes

## [Unreleased](https://github.com/surgiie/blade/compare/v0.2.0...master)
## [v0.2.0] - 2022-11-03

### Added

- `<x-*>` blade component support by @surgiie in https://github.com/surgiie/blade/pulls/1
- `blade()` helper that returns instance by @surgiie in https://github.com/surgiie/blade/pulls/1
-  `Blade::shouldUseCachedCompiledFiles()` and `Blade::useCachedCompiledFiles()` to determine if files are exired in `FileCompiler@isExpired` by @surgiie in https://github.com/surgiie/blade/pulls/1
### Changed
- Remove/update regex that parses directives by @surgiie in https://github.com/surgiie/blade/pulls/1
-  `FileCompiler@isExpired` utilizes new `Blade::shouldUseCachedCompiledFiles()` and `Blade::useCachedCompiledFiles()` to determine if files are exired by @surgiie in https://github.com/surgiie/blade/pulls/1
- include and component nesting support @surgiie in https://github.com/surgiie/blade/pulls/1
- `compile` flushes file finder after compile to ensure cached view property doesnt return unexpected file contents from cached file collisions when using relative paths @surgiie in https://github.com/surgiie/blade/pulls/1

## [v0.1.0] - 2022-10-20

Initial Release
