<div align="center">
  <strong>
    <h2 align="center">Nova Leaflet Field</h2>
  </strong>

  <p align="center">
      <a href="https://php.net/" target="_blank"><img src="https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg"></a>
    </p>

  <p align="center">
    🗺️ Leaflet map field with geo-coding search for Laravel Nova
  </p>

<br>

<p align="center">
    <img src="https://raw.githubusercontent.com/chiiya/nova-leaflet-field/master/.github/images/field.png"
  </p>

</div>
<br />

## Index

<pre>
<a href="#installation"
>> Installation ..................................................................... </a>
<a href="#usage"
>> Usage ............................................................................ </a>
</pre>


## Installation

```bash
composer require chiiya/nova-leaflet-field
# Publish marker icon assets
php artisan vendor:publish --provider="Chiiya\NovaLeafletField\FieldServiceProvider"
```

## Usage

To use the leaflet field, simply specify a label name:

```php
LeafletField::make(__('Geo-Location'))
```

The field will only be displayed on detail and form views. It will attempt to look for `latitude` and `longitude` fields on the associated model to set the initial marker on the map, but you may customize this if your field names are different:

```php
LeafletField::make(__('Geo-Location'))
    ->latitudeField('lat')
    ->longitudeField('lng')
```

The default tile and search provider for Leaflet is OpenStreetMaps. You may configure any search provider supported by [leaflet-geosearch](https://smeijer.github.io/leaflet-geosearch):

```php
LeafletField::make(__('Geo-Location'))
    ->searchProvider(SearchProvider::GOOGLE)
    ->searchProviderKey('api-key')
    ->searchProviderOptions(['language' => 'de', 'region' => 'de'])
```

For customizing the tiles, provide a tile URL:

```php
LeafletField::make(__('Geo-Location'))
    ->tileUrl('https://{s}.tile.osm.org/{z}/{x}/{y}.png')
```

There are a number of options to customize the map behaviour:

```php
LeafletField::make(__('Geo-Location'))
    // Make the map marker draggable
    ->draggable()
    // Customize the geo-search search label
    ->searchLabel(__('Enter address'))
    // Default map zoom
    ->zoom(12)
    // Customize default latitude & longitude
    ->defaultCoordinates(0.0, 0.0)
```

### Validation

When marking the field as `required`, the field will additionally validate that coordinates other than the default ones have been selected. If you wish to disable this behaviour, call the following method:

```php
LeafletField::make(__('Geo-Location'))
    ->allowDefaultCoordinates()
```

You may also customize the error message shown when default coordinates were selected:

```php
LeafletField::make(__('Geo-Location'))
    ->errorMessage(__('Please select a valid location'))
```

### Address Fields Population

When using the Google search provider, you may use the data from the selected location to populate other fields on your model:

```php
LeafletField::make(__('Geo-Location'))
    ->searchProvider(SearchProvider::GOOGLE)
    ->searchProviderKey('api-key')
    ->populateAddress()
    ->populatePostalCode('zip')
    ->populateCity()
    ->populateCountry()
```

The field will attempt to look for other inputs on the page with the default (`address`, `postal_code`, `city`, `country`) or custom names, and fill them with the values received from the Google Geocoding API. For the address, you may specify a custom format:

```php
LeafletField::make(__('Geo-Location'))
    ->searchProvider(SearchProvider::GOOGLE)
    ->searchProviderKey('api-key')
    ->populateAddress()
    // Defaults to '{street_name} {street_number}'
    ->populatedAddressFormat('{street_number}, {street_name}')
```

This will only work when using the Google search provider.



