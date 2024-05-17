# schwab-oauth
For those having difficulty with authentication:

1. Set your App's Callback URL to https://oauth.codeedge.com.br.

2. Wait for the status "Ready For Use."

3. Access https://oauth.codeedge.com.br/?client_id=xxx&client_secret=xxx, replacing xxx with the appropriate values.

    You will be redirected to Schwab's login screen, and after logging in, the JSON will appear on the screen. Simply copy and paste it into a .json file and use the `client_from_token_file` method from the library https://schwab-py.readthedocs.io/en/latest/.

If you want to host the solution on your own, here is the source code: https://github.com/abraaoz/schwab-oauth. In this case, you need to change the line `let redirect_uri = "https://oauth.codeedge.com.br";` at index.html to the chosen address.