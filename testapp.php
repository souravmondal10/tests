<?php
require_once __DIR__ . '/config.php';
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$con = connectDatabase();
if (!$con) {
    die("Can not connect to database");
}
function connectDatabase()
{
    $con = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE, MYSQL_PORT);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        return false;
    }
    return $con;
}

$fakeDataset = [
  'userId' => '555111',
  'useremail' => 'faketest123@test.com',
  'username' => 'fakename'
];
//test step 1
function storeDataIntoRedis($fakeDataset, $redis){
    $userId = $fakeDataset['userId'];
    $useremail = $fakeDataset['useremail'];
    $username = $fakeDataset['username'];
    $redis->set("user_object_" .$userId, json_encode(['username' => $username, 'useremail' => $useremail]));
    $retrivedFromRedis = json_decode($redis->get("user_object_" .$userId));
    if($retrivedFromRedis['username'] == $username) {
        echo 'data stored successfully into redis'.PHP_EOL;
        return true;
    }
    throw new Exception('data storage failed into redis');
}
//test step 2
function checkDatainMysql($fakeDataset, $con){
    $userId = $fakeDataset['userId'];
    $sql = "SELECT * FROM users WHERE userId='$userId'";
    $result = mysqli_query($con, $sql);
    // Fetch all
    $processed_results = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // Free result set
    mysqli_free_result($result);
    mysqli_close($con);
    if(sizeof($processed_results) > 0) {
        echo 'data successfully retrived from mysql'.PHP_EOL;
        return true;
    }
    throw new Exception('data retrival failed from mysql');
}
storeDataIntoRedis($fakeDataset, $redis);
sleep(40);
checkDatainMysql($fakeDataset, $con);
