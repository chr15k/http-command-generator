<?php

declare(strict_types=1);

use Chr15k\HttpCommand\Collections\HttpParameterCollection;

it('adds an item to the collection', function (): void {
    $collection = new HttpParameterCollection;
    $collection->add(key: 'key1', value: 'value1');

    expect($collection->get(key: 'key1'))->toBe(['value1']);
});

it('adds multiple items to the same key', function (): void {
    $collection = new HttpParameterCollection;
    $collection->add(key: 'key1', value: 'value1');
    $collection->add(key: 'key1', value: 'value2');
    $collection->add(key: 'key1', value: 'value3');

    expect($collection->get(key: 'key1'))->toBe(['value1', 'value2', 'value3']);
});

it('retrieves the first item for a key', function (): void {
    $collection = new HttpParameterCollection;
    $collection->add(key: 'key1', value: 'value1');
    $collection->add(key: 'key1', value: 'value2');
    $collection->add(key: 'key1', value: 'value3');

    expect($collection->first(key: 'key1'))->toBe('value1');
});

it('checks if a key exists in the collection', function (): void {
    $collection = new HttpParameterCollection;
    $collection->add(key: 'key1', value: 'value1');

    expect($collection->has(key: 'key1'))->toBeTrue();
    expect($collection->has(key: 'key2'))->toBeFalse();
});

it('retrieves all items in the collection', function (): void {
    $collection = new HttpParameterCollection;
    $collection->add(key: 'key1', value: 'value1');
    $collection->add(key: 'key2', value: 'value2');
    $collection->add(key: 'key1', value: 'value3');

    expect($collection->all())->toBe([
        'key1' => ['value1', 'value3'],
        'key2' => ['value2'],
    ]);
});

it('checks if the collection is empty', function (): void {
    $collection = new HttpParameterCollection;

    expect($collection->isEmpty())->toBeTrue();

    $collection->add(key: 'key1', value: 'value1');

    expect($collection->isEmpty())->toBeFalse();
});

it('merges parameters from another collection', function (): void {
    $collection1 = new HttpParameterCollection;
    $collection1->add(key: 'key1', value: 'value1');
    $collection1->add(key: 'key2', value: 'value2');
    $collection1->add(key: 'key1', value: 'value3');

    $collection2 = new HttpParameterCollection;
    $collection2->add(key: 'key2', value: 'new_value2');
    $collection2->add(key: 'key3', value: 'value3');
    $collection2->add(key: 'key1', value: 'new_value1');
    $merged = $collection1->merge(params: $collection2->all());

    expect($merged->all())->toBe([
        'key1' => ['value1', 'value3', 'new_value1'],
        'key2' => ['value2', 'new_value2'],
        'key3' => ['value3'],
    ]);
});

it('merges parameters from a query string', function (): void {
    $collection = new HttpParameterCollection;
    $collection = $collection->mergeFromQueryString(query: 'key1=value1&key2=value2&key1=value3');

    expect($collection->all())->toBe([
        'key1' => ['value1', 'value3'],
        'key2' => ['value2'],
    ]);
});
