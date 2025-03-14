# PHP Toolkit for IBM i - Modernized Fork ⚠️🚧

## ⚠️ Warning: Experimental Project - Not for Production ⚠️

This is a **fork** of the [PHP Toolkit for IBM i](https://github.com/zendtech/IbmiToolkit), originally developed to facilitate interaction with IBM i systems from PHP applications. This fork is intended as an experimental playground for modernizing the codebase by improving type safety and adhering to modern PHP standards. 

**⚠ Disclaimer: This project is not intended for production use. There is no support, and all changes are purely experimental. ⚠**

## About the PHP Toolkit for IBM i

The PHP Toolkit for IBM i serves as a PHP-based front-end to [XMLSERVICE](http://www.youngiprofessionals.com/wiki/XMLSERVICE). It allows developers to interact with IBM i resources such as RPG, CL, and COBOL programs, as well as run system commands from PHP.

### Key Features:
- Call **RPG, CL, and COBOL** programs from PHP
- Execute interactive commands such as `wrkactjob`
- Support for multiple transport methods: **DB2, ODBC, HTTP**, and more
- Compatibility wrapper for Easycom syntax
- Full support for RPG parameter types: **data structures, packed decimal, output parameters**

XMLSERVICE and the original IBM i Toolkit are bundled with Zend Server and Seiden CommunityPlus+ PHP, but can also be installed separately.

## Goals of This Fork

This fork is purely experimental and aims to:
- Improve **type safety** by adding strict type hints where applicable
- Adopt **modern PHP best practices**, including namespace organization and updated syntax
- Refactor the code for better **maintainability** and **readability**
- Experiment with **PSR standards** (PSR-4 autoloading, PSR-12 coding style, etc.)
- Replace outdated constructs with **newer PHP features**

## Important Notes
- This fork is **not affiliated with Zend or IBM**.
- It is not intended for production and has **no guarantees of stability or security**.
- Contributions are welcome for learning purposes, but PRs may not always be merged if they deviate from the experimental scope.

## Getting Started
If you'd like to explore this experimental version:

```sh
git clone https://github.com/jonkerw85/IbmiToolkit
cd IbmiToolkit
composer install
```

For usage examples, refer to the **[samples directory](https://github.com/zendtech/IbmiToolkit/tree/master/samples)** of the original repository.

## Community & Discussion
Discussions about the original Toolkit happen on GitHub: [IBM i Toolkit Discussions](https://github.com/zendtech/IbmiToolkit/discussions).