<?php
    require_once('vendor/autoload.php');
    // Create .env file
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();

    if(isset($_POST['weatherAppSubmit'])){
        $cityID = $_POST['city'];
    } else{
        $cityID = "Imphal, IN";
    }

    $url = "http://api.openweathermap.org/data/2.5/weather?q=" . $cityID . "&appid=" . getenv('APIKEY');
    $url1 = "api.openweathermap.org/data/2.5/forecast?q=". $cityID ."&appid=" . getenv('APIKEY');

    function json($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data;
    }

    // Current Weather Info
    $data = json($url);

    $name = $data->name .", ". $data->sys->country;
    $temp = $data->main->temp;
    $feels = $data->main->feels_like;
    $desc = ucfirst($data->weather[0]->description);
    $temp_max = $data->main->temp_max;
    $temp_min = $data->main->temp_min;
    $wspeed = $data->wind->speed;
    $wdeg = $data->wind->deg;
    $pressure = $data->main->pressure;
    $humidity = $data->main->humidity;
    $icon = $data->weather[0]->icon;

    // 5 Days weather info
    $data = json($url1);

    for($i = 0; $i < 6; $i++) {
        $info[$i] = array(
            "dt" => $data->list[$i]->dt_txt,
            "temp" => $data->list[$i]->main->temp,
            "feels" => $data->list[$i]->main->feels_like,
            "temp_min" => $data->list[$i]->main->temp_min,
            "temp_max" => $data->list[$i]->main->temp_max,
            "pressure" => $data->list[$i]->main->pressure,
            "humidity" => $data->list[$i]->main->humidity,
            "description" => ucfirst($data->list[$i]->weather[0]->description),
            "icon" => $data->list[$i]->weather[0]->icon,
            "wspeed" => $data->list[$i]->wind->speed,
            "wdeg" => $data->list[$i]->wind->deg,
        );

    }

    function UTCdate($utc){
        $dt = new DateTime("$utc");
        return $dt->format('h:m a d-M');
    }

    function celcius($temp){
        return ($temp - 273.15);
    }

    function windDirection($dir){
        $direct = array('North', 'North-NorthEast', 'NorthEast', 'East-NorthEast', 'East', 'East-SouthEast', 'SouthEast', 'South-SouthEast', 'South', 'South-SouthWest', 'SouthWest', 'West-SouthWest', 'West', 'West-NorthWest', 'NorthWest', 'North-NorthWest', 'North');
        $index = round($dir / 22.5);
        return $direct[$index];
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Weather Application">
    <meta name="author" content="Jackson Konjengbam">
    <title>Weather Report</title>

    <!-- CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center jumbotron">
                <h1>WeatherApp (using weatherOpen API)</h1>
            </div>
        </div><!-- End of Heading -->
        <div class="row mt-5">
            <div class="col-md-12">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <div class="form-group row text-center">
                        <label for="address" class="col-sm-2 col-form-label">City name:</label>
                        <input type="text" class="form-control col-sm-8" id="address" name="city" placeholder="Enter city name (Example: Imphal, IN)" required>
                        <button type="submit" name="weatherAppSubmit" class="btn btn-primary col-sm-2">Submit</button>
                    </div>
                    <div class="text-center">
                        <p>Selected: <strong id="address-value"><?php echo $cityID;?></strong></p>
                    </div>
                </form>
            </div>
        </div> <!-- End of City Form -->
        <div class="row mt-5 mb-5">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header text-center">
                        <h1><?php echo $name;?></h1>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mt-4">
                            <div class="col-md-6 mt-2 mb-2">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <img src="<?php echo 'http://openweathermap.org/img/wn/'. $icon .'@2x.png?';?>" alt="">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="col-md-12">
                                            <h2><?php $c = celcius($temp); echo $c;?>&deg;C</h2>
                                        </div>
                                        <div class="col-md-12">
                                            <small>feels like <?php $c = celcius($feels); echo $c;?>&deg;C</small>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- End of section one -->
                            <div class="col-md-3">
                                <h2><?php echo $desc;?></h2>
                                <p>Max: <?php $c = celcius($temp_max); echo $c;?>&deg;C</p>
                                <p>Min: <?php $c = celcius($temp_min); echo $c;?>&deg;C</p>
                            </div>
                            <div class="col-md-3">
                                <p>Wind: <?php echo $wspeed;?> m/s</p>
                                <p>Direction: <?php echo windDirection($wdeg). " ( ". $wdeg ." deg )";?></p>
                                <p>Pressure: <?php echo $pressure;?> hpa</p>
                                <p>Humidity: <?php echo $humidity;?>%</p>
                            </div>
                        </div> <!-- End of first row -->
                    </div>
                </div>
            </div>
        </div> <!-- End of Weather info -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Weather report for the last 18 hours</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                                for($i=0; $i<3; $i++) {
                                    echo "
                                    <div class=\"col-md-4 mt-2\">
                                        <div class=\"card\">
                                            <div class=\"card-body text-center\">
                                                <p><span class=\"font-weight-bold\">Date:</span> ". UTCdate($info[$i]['dt']) ."</p>
                                                <img src=\"http://openweathermap.org/img/wn/". $info[$i]['icon'] ."@2x.png?\" alt=''>
                                                <p class=\"font-weight-bold\">". $info[$i]['description'] ."</p>
                                                <p style='margin-bottom: 0;'><span class=\"font-weight-bold\">Temp:</span> ". celcius($info[$i]['temp']) ." &deg;C</p>
                                                <small>feels like ". celcius($info[$i]['feels']) ." &deg;C</small>
                                                <p style='margin-top: 10px'><span class=\"font-weight-bold\">Temp_min:</span> ". celcius($info[$i]['temp_min']) ." &deg;C</p>
                                                <p><span class=\"font-weight-bold\">Temp_max:</span> ". celcius($info[$i]['temp_max']) ." &deg;C</p>
                                                <p><span class=\"font-weight-bold\">Pressure:</span> ". $info[$i]['pressure'] ." hpa</p>
                                                <p><span class=\"font-weight-bold\">Humidity:</span> ". $info[$i]['humidity'] ."%</p>
                                                <p><span class=\"font-weight-bold\">Wind:</span> ". $info[$i]['wspeed'] ." m/s</p>
                                                <p><span class=\"font-weight-bold\">Direction:</span> ". windDirection($info[$i]['wdeg']) ." ( ". $info[$i]['wdeg'] ." deg )</p>
                                            </div>
                                        </div>
                                    </div>
                                    ";
                                }
                                for($i=3; $i<6; $i++) {
                                    echo "
                                    <div class=\"col-md-4 mt-2\">
                                        <div class=\"card\">
                                            <div class=\"card-body text-center\">
                                                <p><span class=\"font-weight-bold\">Date:</span> ". UTCdate($info[$i]['dt']) ."</p>
                                                <img src=\"http://openweathermap.org/img/wn/". $info[$i]['icon'] ."@2x.png?\" alt=''>
                                                <p class=\"font-weight-bold\">". $info[$i]['description'] ."</p>
                                                <p style='margin-bottom: 0;'><span class=\"font-weight-bold\">Temp:</span> ". celcius($info[$i]['temp']) ." &deg;C</p>
                                                <small>feels like ". celcius($info[$i]['feels']) ." &deg;C</small>
                                                <p style='margin-top: 10px'><span class=\"font-weight-bold\">Temp_min:</span> ". celcius($info[$i]['temp_min']) ." &deg;C</p>
                                                <p><span class=\"font-weight-bold\">Temp_max:</span> ". celcius($info[$i]['temp_max']) ." &deg;C</p>
                                                <p><span class=\"font-weight-bold\">Pressure:</span> ". $info[$i]['pressure'] ." hpa</p>
                                                <p><span class=\"font-weight-bold\">Humidity:</span> ". $info[$i]['humidity'] ."%</p>
                                                <p><span class=\"font-weight-bold\">Wind:</span> ". $info[$i]['wspeed'] ." m/s</p>
                                                <p><span class=\"font-weight-bold\">Direction:</span> ". windDirection($info[$i]['wdeg']) ." ( ". $info[$i]['wdeg'] ." deg )</p>
                                            </div>
                                        </div>
                                    </div>
                                ";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End of 5 days weather info -->
    </div> <!-- End of Container -->

</body>
</html>