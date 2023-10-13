# /inbox

This documentation provides information about the `/inbox` command in your chess application, which is used for managing messages and notifications related to chess games.

## Overview

The `/inbox` command is a versatile feature in your chess application that allows users to manage their in-app messages, notifications, and game-related communications. It is designed to enhance user engagement and facilitate effective communication between chess players.

## Parameters
| Name | Type | Description | Required? | Default Value  | Example Values    |
|---|---|---|---|---|---|---|
| inboxId | String | The ID of the Inbox. This can be obtained from [here](https://app.sendgrid.com
/settings/mail_settings) by clicking on "Manage API Keys" and then selecting "Inboxes". | Yes | None   | <KEY
> |

## Return Value
Returns an object containing a `message` property with value as `"success"` if operation was successful, or else returns error message string describing reason for failure.
Returns an object containing a list of messages, or null if there are no messages to return (i.e., all have been read).
The return value will depend on the format requested:
- JSON, if no query parameters are provided (i.e., `GET https://{host}/api/{version}/inboxes`)
- XML, if a valid query parameter is specified (e.g., `GET https://{host}/api/{version}/inboxes?
format=xml` or `GET https://{host}/api/{version}/inboxes.{format}`)
If successful, returns an HTTP status code of 200 and a representation of the Inbox object(s). If unsuccessful,
The function returns the result of the command, which is typically an array, string, or JSON response.

## Exceptions
If there was an error with your request, SendGrid will throw one of the following exceptions:
- InvalidQueryParameter - Your request could not be processed because of a validation issue, such as an invalid date range.
- AuthorizationError - Your authorization headers were not formatted correctly
- InvalidQueryParameter - You specified an invalid query paramter for the request
- HTTPError - A non-20x status code was returned

InvalidArgumentException: Thrown if the action or options are invalid.

## Usage

The `/inbox` command in your chess application has the following syntax:

```php
/**
 * /inbox Command for Chess
 *
 * This command is used for managing messages and notifications in your chess application's inbox.
 *
 * @param string $action The action to perform, e.g., 'send', 'read', 'delete'.
 * @param array $options Additional options or parameters for the command.
 *
 * @return mixed The result of the command, typically an array, string, or JSON response.
 *
 * @throws InvalidArgumentException If the action or options are invalid.
 */
```
function inbox_chess($action, $options = []) {
    // Your command implementation here
    // Implement inbox-related actions based on the $action and $options parameters.
    
    // Return the result of the command
    return $result;
}
```