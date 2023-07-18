# API Proxy Filter

This simple application allows requesting single values of any external JSON-API endpoint, intended for use in 
[StreamElements](https://streamelements.com/), but of course not limited to.

## Example Usage

```bash
$ curl https://example.com/api?url=https://api.example.com/some/endpoint&field=content.items[2].name
Hello
```
when the fictional endpoint returns the following JSON:

```json
{
    "content": {
        "items": [
            {
                "name": "Hi"
            },
            {
                "name": "Howdy"
            },
            {
                "name": "Hello"
            },
            {
                "name": "G'day"
            }
        ]
    }
}
```

## Installation

Just clone the repository and execute `composer install`.

Only whitelisted hosts can be queried, the whitelist can be configured in <config/fetcher.php> 
