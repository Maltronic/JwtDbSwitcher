Provides listeners for LexikJWTAuthenticationBundle events to enable the setting of doctrine's default database
upon authentication. This allows the abstraction of database connection information in multi-database systems where the 
relevant database is only discernible upon user authentication.

Usually doctrine's "default" database is specified upon application initialisation and other database connections must
be specified by name. This bundle enables the setting of doctrine's "default" database when a user authenticates using
the LexikJWTAuthentication bundle (http://github.com/lexik/LexikJWTAuthenticationBundle). A specified database is used
for authentication as per normal, but each user can be given one or more databases to switch to upon authentication. In 
addition, other database specific parameters may be assigned as well.

Contributing
------------

See [CONTRIBUTING](CONTRIBUTING.md) file.


Credits
-------

* Maltronic <maltronic.email@gmail.com>
* [All contributors](https://github.com/maltronic/JwtDbSwitcher/graphs/contributors)

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE