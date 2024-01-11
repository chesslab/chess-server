# Features

PHP Chess Server is an asynchronous PHP server using the PHP Chess library provides functionality to play chess online.

Similar to the [Chess API](https://chess-api.readthedocs.io/en/latest/), it can be hosted on a custom domain. The main difference between both is that the Chess API endpoints may take few seconds to execute like a file download or a database query. The PHP Chess Server commands are intended to run faster; its event-driven, non-blocking architecture allows to handle multiple concurrent connections in an efficient way.
