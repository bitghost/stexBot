# stexBot

Step 1: Clone The Repo

git clone https://github.com/bitghost/stexBot.git

Step 2: Bot Configuration

Configure the bot by adding your API key and API secret in stexBot.php (see code block below)
Then add the pairs you would like to trade (ie "ETH_BTC") in the pairs array
And finally select your trade direction. If you would like to accumulate the listed pairs, use "BUY" as the trade direction. If you would like to liquidate your position use "SELL" as the direction.
Currently if you do not have enough funds to execute a buy the trade will fail and move on to the next.

// USER CONFIGURATION STARTS HERE<br>
$key = ''; // API key<br>
$secret = ''; // API secret<br>
$pairsArray = array("ETHO_BTC", "ETH_BTC"); // Array for pairs to trade<br>
$orderDirection = "BUY"; // BUY or SELL<br>
// USER CONFIGURATION ENDS HERE<br>

Step 3: Add a Cron Job
contab -e

Use the following cron command to execute the script every 5 minutes. (other time frames can be used but 5 minutes is a good starting point)
*/5 * * * * /usr/bin/php /home/<username>/stexBot/stexBot.php 
