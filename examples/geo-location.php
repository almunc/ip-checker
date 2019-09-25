<html>
<head>
    <title>Geo Location example</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<center>
<?php
require("../ip.php");

$IP = new IP();

if($res = $IP->ipapi()){
    echo "<h3>Your data according to <a href='//ipapi.com' target='_blank'>ipapi</a>:</h2><table border='1' style='text-align: center;'><tr><th>Property</th><th>Value</th>";
    foreach($res as $key => $value){
        echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
    }
}
else{
    echo "<div class='alert alert-danger' role='alert'>Failed to lookup your data</div>";
}
?>
</center>
</body>
</html>
