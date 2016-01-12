# Sterling Responses

The responses package give you standardised responses for all your calls whether they are server side or client side

### Installation

Add the following repository to your ``` composer.json ```. You will have access as long as you belong to the Sterling team on Bitbucket.

``` sh
  "repositories": [
    {
      "type": "composer",
      "url": "http://satis.sterling-design.co.uk"
    }
  ]
```

``` composer require sterling/responses```

Register the service provider:
``` Sterling\Responses\SterlingResponsesServiceProvider ```

``` composer dump-autoload ```


### Usage
You can use the controller responses ``` doStore ``` and ``` doUpdate ``` to standardise your actions for a resourceful controller (store and update respectively).

The ``` SterlingResponse ``` class takes care of organising your content and returning it appropriate either as JSON or a as a redirect with a message in the session.
 
The methods are ``` SterlingResponse::success() ``` and ``` SterlingResponse::failure() ``` passing in any arguments you want to return back in the response.