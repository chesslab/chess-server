# PHP Chess Server

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/license/mit/)
[![Contributors](https://img.shields.io/github/contributors/chesslablab/chess-server)](https://github.com/chesslablab/chess-server/graphs/contributors)

PHP Chess Server is an asynchronous PHP server that provides services of data and chess functionality to play online over a WebSocket connection.

| Port | Service Name | Description |
| ---- | ------------ | ----------- |
| 9443 | data | JSON-formatted data |
| 8443 | game | Chess functionality |
| 7443 | binary | Binary data |
| 6443 | auth | Authentication functionality |

## Object-Oriented

The socket, the chess commands, the game modes and the asynchronous tasks are all implemented using OOP principles.

## Async PHP Frameworks

The flexible architecture of PHP Chess Server allows support for multiple async PHP frameworks, with the default one being Workerman.

- Workerman
- Ratchet

The Spatie async library providing a wrapper around PHP's PCNTL extension is used in order for asynchronous commands to not block the event loop.
