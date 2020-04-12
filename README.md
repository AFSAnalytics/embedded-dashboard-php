# AFS Analytics embedded dashboard

This PHP Module allows you to embed AFS Analytics
dashboard in your admin with just a few lines of code.




## Install

```
composer require afsanalytics/dashboard
```

## Requirements

PHP >= 7.0

## Getting started






### Introduction
 

Please note that the page displaying AFS Analytics
dashboard **should not** be public -- unless you want
your stats to be public. 



### Displaying the Dashboard

Including the dashboard on a page is done by creating
a container element, and then calling the render method.

```php

print '<div id=my_custom_id></div>';

$db = new \AFSAnalytics\Dashboard\Controller( YOUR_API_KEY );
$db->setParentSelector('#my_custom_id')
   ->render()
;


```



### Running the Ajax Server

In addition to inserting the code displaying the dashboard,
you need to run the ajax server responsable for calling 
AFS Analytics REST API.

This can be done with just two lines of codes:

```php

$db = new \AFSAnalytics\Dashboard\Controller( YOUR_API_KEY );
$db->runAJAXServer();

```

This code can be inserted on the same page, or on a separate one.
In either case it **must be** placed before any outpout. 

 


### Passing data to the Ajax Server

In order to secure access to the Ajax Server, 
you might want to add some custom data to all 
Ajax calls. 

You can do this via javascript:

```js
AFSA.hook.prepareAjaxData = function (data) {
				data.my_property = my_value;
			};
```






### Obtaining an API Key

You can create an API Key at [https://dev.afsanalytics.com](https://dev.afsanalytics.com/en/manage/api/keys.php)

Please note that a valid AFS Analytics account will be required,
as a subscription including API Access.



### Methods of interest

#### setParentSelector($selector)

Embed the dashboard inside the specified selector.

#### render( $options = [] )

Return the dashboard HTML code.

```php
$db ->setParentSelector('#my_custom_id')
    ->render([
        'css' => string // custom css to be inserted - optional
        ])
```

#### disableECommerce()

Disable all eCommerce related reports.

### setLangage( $lng )

Set dashboard langage.

Currently supported $lng values : 'en', 'fr'



## Changelog

See the [project changelog](./CHANGELOG.md)

## Contributing

Contributions are always welcome. 


## Support

Please email `dev@afsanalytics.com`.


## License

This package is released under the MIT License. See the bundled [LICENSE](./LICENSE) file for details.
