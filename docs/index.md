# PHP Chess Server

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Contributors](https://img.shields.io/github/contributors/chesslablab/chess-server)](https://github.com/chesslablab/chess-server/graphs/contributors)

PHP Chess Server is an asynchronous PHP server that provides functionality to play chess online over a WebSocket connection as well as over a TCP connection.

Similar to the [Chess API](https://chess-api.docs.chesslablab.org/), it can be hosted on a custom domain. However, while the API endpoints may take few seconds to execute — for example, a file download or a database query — the event-driven, non-blocking architecture of the chess server allows to handle multiple concurrent connections in an efficient way.

The chess commands are intended to run very quickly almost in real-time.

## Object-Oriented

The socket, the game modes and the chess commands are all implemented using OOP principles.

## Lightweight

Dependencies required:

- PHP Chess for chess functionality.
- Ratchet for asynchronously serving WebSockets.
- PHP-JWT for encoding and decoding JSON Web Tokens (JWT).
- PHP dotenv for loading environment variables.
- Monolog for logging commands.
