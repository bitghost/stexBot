<?php
use Stocks\ApiVersion\Two;
require 'vendor/autoload.php';

// USER CONFIGURATION STARTS HERE
$key = ''; // API key
$secret = ''; // API secret
$pairsArray = array("ETHO_BTC", "ETH_BTC"); // Array for pairs to trade
$orderDirection = "BUY"; // "BUY" or "SELL"
// USER CONFIGURATION ENDS HERE

$stocks = new Two($key, $secret, 'https://app.stex.com/api2', false);

foreach($pairsArray as $key => $pair) {
    cancelOrders($pair, $stocks);
    initiateNewOrders($pair, $orderDirection, $stocks);
}

function cancelOrders($pair, $stocks){
    $orderID = 1;
    $loopCount = 0;
    while($orderID != null && $loopCount < 5)
    {
        $activeOrderInfo = $stocks->getActiveOrders($pair, null, null, null, null, null, null, null, null, "OWN");
        $jsonOrderInfo = json_encode($activeOrderInfo);
        $orderInfoArray = json_decode($jsonOrderInfo);
        $loopingArray = $orderInfoArray->data;
        $orderID = null;
        $loopingArray = (array) ($loopingArray);
        foreach($loopingArray as $key => $orders)
        {
            $orderID = $key;
            echo "\n\nOrder ID: ".$orderID."\n\n";

        if($orderID != null)
        {
            $loopCount++;
            echo $orderID." >> Order Cancelled!\n";
            $stocks->setCancelOrder($orderID);
            sleep(1);
        }}
    }
}

function initiateNewOrders($pair, $orderDirection, $stocks){
    $orderID = 1;
    $loopCount = 0;
    if($loopCount < 5){
        sendOrder($pair, $stocks, $orderDirection);
    }
    sleep(1);
}

function sendOrder($pair, $stocks, $orderType){
    $orderBookInfo = file_get_contents('https://app.stex.com/api2/orderbook?pair='.$pair);
    $orderBook = json_decode($orderBookInfo, true);
    $topBuyPrice = $orderBook['result']['buy'][0]['Rate'];
    $topSellPrice = $orderBook['result']['sell'][0]['Rate'];
    $accountInfoArray = $stocks->getInfo();
    $holdingsArray = $accountInfoArray->data->funds;
    $inProcessHoldingsArray = $accountInfoArray->data->hold_funds;
    $currentHoldings = 0;
    $BTCHoldings = 0;
    foreach ($holdingsArray as $symbol => $amount) {
        $splitPair = explode("_", $pair);
        if($symbol == $splitPair[0]) {
            $currentHoldings = $amount;
        }
        if($symbol == "BTC") {
            $BTCHoldings += $amount;
        }
    }
    foreach ($holdingsArray as $symbol => $amount) {
        $splitPair = explode("_", $pair);
        if($symbol == $splitPair[0]) {
            $currentHoldings = $amount;
        }
        if($symbol == "BTC") {
            $BTCHoldings += $amount;
        }
    }
    if ($orderType == "SELL") {
        //SEND SELL ORDER
        $orderAmount = getOrderAmount($orderBook, $currentHoldings, $orderType, $pair);
        $newOrderAmount = (string) round($orderAmount, 7);
        $newOrderPrice = getOrderPrice($orderBook,  $orderType);
        var_dump($stocks->setTrade("SELL", $pair, $newOrderAmount, $newOrderPrice));
    }
    else if ($orderType == "BUY") {
        //SEND BUY ORDER
        $orderAmount = getOrderAmount($orderBook, $currentHoldings, $orderType, $pair);
        $newOrderAmount = (string) round($orderAmount, 7);
        $newOrderPrice = getOrderPrice($orderBook,  $orderType);
        var_dump($stocks->setTrade("BUY", $pair, $newOrderAmount, $newOrderPrice));
    }
}

function getOrderAmount($orderBook, $currentHoldings, $orderType, $pair){
    $openOrdersSell = $orderBook['result']['sell'];
    $openOrdersBuy = $orderBook['result']['buy'];
    $openOrderTotal = 0;
    $averageCount = 0;
    for($i = 0; $i < count($openOrdersSell) && $i < 5; $i++){
      	$openOrderTotal += $openOrdersSell[$i]['Quantity'];
        $averageCount++;
    }
    for($i = 0; $i < count($openOrdersBuy) && $i < 5; $i++){
      	$openOrderTotal += $openOrdersBuy[$i]['Quantity'];
        $averageCount++;
    }
    $openOrderAverage = ($openOrderTotal / $averageCount) * 10;
    $orderAmount = (float) (($openOrderAverage - ($openOrderAverage * 0.9)) + (($openOrderAverage - ($openOrderAverage * 0.98)) - ($openOrderAverage - ($openOrderAverage * 0.9))) * mt_rand(0, 32767)/32767);

    $orderAmount = $orderAmount * 0.25;
    return $orderAmount;
}
function getOrderPrice($orderBook,  $orderType){
    if($orderType == "SELL"){
        $openOrders = $orderBook['result']['sell'];
    }
    else{
        $openOrders = $orderBook['result']['buy'];
    }
    $topOrderPrice = $openOrders[0]['Rate'];
    if($orderType == "BUY"){
        $newOrderPrice = (string) (number_format(($topOrderPrice + 0.00000001), 8, '.', ''));
    }
    else{
        $topOrderPrice = $openOrders[0]['Rate'];
        $newOrderPrice = (string) (number_format(($topOrderPrice - 0.00000001), 8, '.', ''));
    }
    echo "New Order Price: ".$newOrderPrice."\n";
    return $newOrderPrice;
}
?>
