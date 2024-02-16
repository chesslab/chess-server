# PHP Chess Server

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/license/mit/)
[![Contributors](https://img.shields.io/github/contributors/chesslablab/chess-server)](https://github.com/chesslablab/chess-server/graphs/contributors)

PHP Chess Server is an asynchronous PHP server that provides functionality to play chess online over a WebSocket connection.

Similar to the [PHP Chess API](https://chess-api.docs.chesslablab.org/), it can be hosted on a custom domain. However, while the API endpoints may take few seconds to execute — for example, a file download or a database query — the event-driven, non-blocking architecture of the chess server allows to handle multiple concurrent connections in an efficient way.

The chess commands are intended to run very quickly almost in real-time.

## Object-Oriented

The socket, the game modes and the chess commands are all implemented using OOP principles.

## Async PHP Frameworks

The flexible architecture of PHP Chess Server allows support for multiple async PHP frameworks, with the default one being Workerman.

- Workerman
- Ratchet
