# stexBot

<B>Step 1: Clone The Repo</B>

git clone https://github.com/bitghost/stexBot.git

<B>Step 2: Bot Configuration</B>

Configure the bot by adding your API key and API secret in stexBot.php (see code block below)<br>
Then add the pairs you would like to trade (ie "ETH_BTC") in the pairs array<br>
And finally select your trade direction. If you would like to accumulate the listed pairs, use "BUY" as the trade direction. If you would like to liquidate your position use "SELL" as the direction.<br>
Currently if you do not have enough funds to execute a buy the trade will fail and move on to the next.<br>

// USER CONFIGURATION STARTS HERE<br>
$key = ''; // API key<br>
$secret = ''; // API secret<br>
$pairsArray = array("ETHO_BTC", "ETH_BTC"); // Array for pairs to trade<br>
$orderDirection = "BUY"; // BUY or SELL<br>
// USER CONFIGURATION ENDS HERE<br>

<B>Step 3: Add a Cron Job</B> <br>
crontab -e

Use the following cron command to execute the script every 5 minutes. (other time frames can be used but 5 minutes is a good starting point)<br>
*/5 * * * * /usr/bin/php /home/<username>/stexBot/stexBot.php 
