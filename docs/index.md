# Features

PHP Chess Server is an asynchronous PHP server that provides functionality to play chess online over a WebSocket connection.

Similar to the [Chess API](https://chess-api.readthedocs.io/en/latest/), the PHP Chess Server can be hosted on a custom domain. However, the API endpoints may take few seconds to execute like a file download or a database query while the event-driven, non-blocking architecture of the chess server allows to handle multiple concurrent connections in an efficient way.

The chess commands are intended to run very quickly almost in real-time.

## Lightweight

Dependencies required:

- Ratchet for asynchronously serving WebSockets.
- PHP Chess for chess functionality.
- PHP-JWT for encoding and decoding JSON Web Tokens (JWT).
- PHP dotenv for loading environment variables.
- Monolog for logging commands.
- SleekDB for temporarily storing the correspondence chess inboxes.
