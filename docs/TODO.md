AssetsMinify - TODO LIST
============

This is the only one todo list for the *dev branch*.

The *dev branch* is responsible for all new features and code refactoring and re-organization on AssetsMinify.

The next version released will be 2.0.0.


Todo
-------------

### Re-organization

- Rewrite unit-tests and write code using TDD
- Manage Jenkins CI integration
- Reorganize the Init class to be splitted in more classes

### Bugfix

- [Built-in scripts should also be included](https://wordpress.org/support/topic/built-in-scripts-that-should-be-enqueued-in-footer-are-enqueued-in-header)
- Test on SSL

### Enhancement

- Enable SASS, not only SCSS
- Enable Stylus
- [Allow CDN configuration for assets inclusion](https://github.com/acarbone/AssetsMinify/issues/23)
- [Allow different media types](https://wordpress.org/support/topic/media-type)
- [Allow to exclude css/js](https://github.com/acarbone/AssetsMinify/issues/21), [2](https://github.com/acarbone/AssetsMinify/issues/22)
- Reorganize the admin page
- Write script to help manage git->svn repo (only some files need to be copied)
- Compare JSMin to patchwork/jsqueeze

### New features

- Image optimization tool: default flag? Helper function for coding themes?
- Analyze better optimizations to speed up WP


Done
-------------

- Dependency management with composer for external libraries