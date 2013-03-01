# Framework

Framework contains basic functionality that can be shared across our various projects. Some of the current features include:

* Zend Framework 1.10
* Scaffold code generators
    * Rowset scaffold creates Zend_Db_Table, Zend_Db_Rowset, and Zend_Db_Row classes based off of your database tables
* DDM_Form and DDM_Form_Elements
* Json RPC Client
* Authorize.net Payment Classes
* Nonce and Signature Security classes
* Extended PHPUnit Test Classes
* Selenium 2 PHP WebDriver
* DDM_Mail

## Submission Guidelines

All changes to the framework must come through Pull Requests to allow for peer review. Pull Requests
should only be sent to the stage branch. Do not send pull requests to master.

All submitted code must strictly follow the [Zend Coding Standards][zcs].

## Init Submodules

The framework contains git submodules. When you first clone the repository, you'll need to set these up.

```
git submodule init
git submodule update
```

[zcs]: http://framework.zend.com/manual/en/coding-standard.html