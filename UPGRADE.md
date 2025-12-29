
# Upgrade guide

## From version 2.x to 3.0

Update all usages of `RequestConstraint`

**Before**
```php
new RequestConstraint(
    [
        'query' => ..., 
        'request' => ...,  
        'attributes' => ..., 
        'allowExtraFields' => true
    ]
);
```

**After**
```php
new RequestConstraint(
    query: ...,
    request: ...,
    attributes: ...,
    allowExtraFields: true
);
```
