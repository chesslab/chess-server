# /online_games
// Route for online games. This will be used to create, join or leave a game.
Route::get('/online_games', [OnlineGameController::class,'index'])->name('online-game');
Route::post('/create/new/game',[OnlineGameController::class,'store'])->name('create-game');
Route::post('/join/game',[OnlineGameController::class,'show'])->name('join-game');

## Description
This is the main route that handles all of our requests and routes them accordingly based on what they are requesting from us. We have 3 different types

# Parameters
| Name | Type   | Required  | Default Value | Description
|:----:|:------:| :--------: |:-------------:| ------------
| id    | string | yes       |               |The ID of the user that is trying to make an account on our webpag
| id   | string | true       | null           | The unique identifier of the game
| id    | string | yes       |               | The ID of the user you want to delete from the database
| name     | String | Yes       |               | The username of the user that is creating this new game.
| id    | int    | true      | null         | The unique identifier of the game you want to join.


This is the route that handles all of our requests related to creating and joining an online game. The first thing we do in this controller is check if
1) The user can view the list of all available games on the website by clicking on "View Online Games" button in the navigation bar at top
This set of routes corresponds to the handling of online multiplayer games.
# GET /online_games
This route shows the list of available online games.
# POST /create/new/game
This route creates new online games and returns an id that can then be joined by other players using this same endpoint with different parameters (see below). This is the route for creating new online games and joining them as players. The routes are protected by middleware that checks if the user has an account before.
This route creates a new game and returns it in JSON format. It takes as parameter the name of the game that should be created.

This route creates a new game and returns it in JSON format. It takes as parameter the name of the game that should be created.

This route is intended to handle the creation of a new online game by authenticated users. With this, a user can create a game and start playing

# POST /register
Register new user in database and redirect him back on login page with success message if registration was successful otherwise show error messages about failed validation rules.


# Usage
```text
$response = $client->request(
    'POST',
    '/api/v1/auth/register',
    ['form_params' => ['username'=>'test','email'=>'test@gmail.com','password'=>'secret']]
    );
    var_dump($response);exit;
    
# Responses
You should receive a JSON response with the following structure:
{
    "status": "success",
    }
    # Error responses
    If the provided email or username already exists, you will receive an error message:
    {
        "error" : "Email or Username already taken."
        }
        Similarly, if there are missing parameters in your request body (such as when password isn't included), you will also receive an error message:
        
        "error" : "Missing Parenthesis"
```
    
# Login
To login as a registered user use the following endpoint and provide your credentials in order for authentication.
To login into your account use the following endpoint:
# POST /login
The request body must contain valid credentials in order for authentication to succeed (see below). The server responds by sending back a JWT token that can be taken
The request body must contain the credentials for logging in (the same as when registering): json
    
    



        
