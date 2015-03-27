AssetsMinify - TODO LIST
============

This is the only one todo list for the *dev branch*.

The *dev branch* is responsible for all new features and code refactoring and re-organization on AssetsMinify.

The next version released will be 2.0.0.


Todo
-------------

### Enhancement

- [Allow CDN configuration for assets inclusion](https://github.com/acarbone/AssetsMinify/issues/23)
- [Allow different media types](https://wordpress.org/support/topic/media-type)
- [Allow to exclude css/js](https://github.com/acarbone/AssetsMinify/issues/21), [2](https://github.com/acarbone/AssetsMinify/issues/22)
- Reorganize the admin page
- Write script to help manage git->svn repo (only some files need to be copied)
- Compare JSMin to patchwork/jsqueeze
- [Should be used Cache Busting from Assetic?](https://github.com/kriswallsmith/assetic#user-content-cache-busting)

### New features

- Image optimization tool: default flag? Helper function for coding themes?
- Analyze better optimizations to speed up WP (gz?)


Done
-------------

### Re-organization

- Dependency management with composer for external libraries
- Manage Travis CI integration
- Reorganize the Init class to be splitted in more classes
- Rewrite unit-tests and write code using TDD

### Bugfix

- [Built-in scripts should also be included](https://wordpress.org/support/topic/built-in-scripts-that-should-be-enqueued-in-footer-are-enqueued-in-header)
- Test on SSL

### Enhancement

- [SASS Placeholder support](https://wordpress.org/support/topic/scss-compiler-chokes-on-placeholder-selectors)
- Enable SASS, not only SCSS