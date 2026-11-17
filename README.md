# Council Payments

This directory contains the application code for the D3R developer practical test. The application will display payments made by Chichester District Council to suppliers in October 2010, using the supplied database dump.

The listing should follow the supplied design and support searching and pagination. PHP classes in this directory use the `Local\CouncilPayments` namespace and are loaded through Composer's PSR-4 autoloader.

## Entry point

Run the listing controller from the project's `index.php`. The controller supplies the placeholder data and loads the layout:

```php
(new \Local\CouncilPayments\Controller\Listing())->render();
```

## Tests

Run the tests from the project root (`devpracticaltest-2.0`):

```sh
vendor/bin/phpunit --bootstrap vendor/autoload.php src/Local/CouncilPayments/tests
```
