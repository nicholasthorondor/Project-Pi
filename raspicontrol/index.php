<?php
    // Starts a session.
    session_start();

    //-------------
    //VARIABLES
    //-------------
    // Check status of each required service and assign true or false variables.
    // Default values.
    $visualiserOn = false;
    $ambilightOn = false;
    $moodlightOn = false;
    $slideshowOn = false;

    // Checks to see if the visualisation software is running, and updates the status variable accordingly.
    // Has to incorporate a round about method of pipes as it is a custom script being searched for.
    $visualiserCheck = shell_exec("/bin/ps -ef | /bin/grep ^root | /bin/grep 'visualization.py'");
    if (empty($visualiserCheck)) {
        $visualiserOn = false;
    } else {
        $visualiserOn = true;
    }

    // Checks to see if the mood lighting is active, and updates the status variable accordingly.
    // Has to incorporate a round about method of pipes as it is a custom script being searched for.
    $moodlightCheck = shell_exec("/bin/ps -ef | /bin/grep ^root | /bin/grep 'mood_light_colour.py'");
    if (empty($moodlightCheck)) {
        $moodlightOn = false;
    } else {
        $moodlightOn = true;
    }

    // Checks to see if ambilight is active, and updates the status variable accordingly.
    $ambilightCheck = shell_exec("/bin/systemctl status hyperion.service | /bin/grep 'running'");
    if (empty($ambilightCheck)) {
        $ambilightOn = false;
    } else {
        $ambilightOn = true;
    }

    // Checks to see if the slideshow is active, and updates the status variable accordingly.
    $slideshowCheck = shell_exec("/usr/bin/pgrep fbi");
    if (empty($slideshowCheck)) {
        $slideshowOn = false;
    } else {
        $slideshowOn = true;
    }

    // Stores on/off variables in a session variable to carry across to processing script.
    $_SESSION["visualiserOn"] = $visualiserOn;
    $_SESSION["ambilightOn"] = $ambilightOn;
    $_SESSION["moodlightOn"] = $moodlightOn;
    $_SESSION["slideshowOn"] = $slideshowOn;

    // Output variable to storing result of control execution.
    if (isset($_SESSION["output"])) {
        $output = $_SESSION["output"];
        // Clears the session variable once it has been displayed to prevent lasting through page refreshes.
        unset($_SESSION["output"]);
    } else {
        $output = "";
    }

    // Read last used colour of the Pi's mood lights.
    $colourfilearray = file("mood_light_last_colour.txt", FILE_IGNORE_NEW_LINES);
    if ($colourfilearray) {
	$colour = $colourfilearray[0] . "," . $colourfilearray[1] . "," . $colourfilearray[2];
     } else {
	$colour = "255,255,0"; // Random default colour if no file is present.
     }

    //-------------
    //FUNCTIONS
    //-------------
    // Converts a boolean to an on/off status string accordingly.
    function ConvertStatus($status) {
        if ($status) {
            return "On";
        } else {
            return "Off";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta lang="en">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raspberry Pi Control Panel</title>
    <!-- Sets favicon. -->
    <link rel="shortcut icon" type="image/png" href="images/favicon.png"/>
    <!-- Bootstrap CDN. -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Normalize CSS. -->
    <link rel="stylesheet" type="text/css" href="css/normalize.css">
    <!-- Custom CSS. -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <!-- Header image. -->
        <img src="images/raspberrypi.png" alt="Raspberry Pi Logo">
    </header>
        <!-- Output message. -->
        <div class="output">
            <hr class="output-hr">
            <p><?php echo $output ?></p>
            <hr class="output-hr">
        </div>
    <section>
        <!-- Control buttons. -->
        <form method="POST" action=control.php>
            <div class="container">
                <!-- System controls. -->
                <div class="row control-name">
                    <h2 class="col-sm">System Controls</h2>
                </div>
                <div class="row">
                    <div class="col-sm sys-control-shutdown">
                        <input type="submit" name="shutdown" value="Shutdown" class="btn btn-outline-primary btn-lg"">
                    </div>
                    <div class="col-sm sys-control-reboot">
                        <input type="submit" name="reboot" value="Reboot" class="btn btn-outline-primary btn-lg">
                    </div>
                </div>
                <hr>
                <!-- LED controls. -->
                <div class="row control-name">
                    <h2 class="col-sm">LED Controls</h2>
                </div>
                <div class="row">
                    <div class="col-sm led">
                        <input type="submit" name="visualiser" value="Music Visualiser On/Off" class="btn btn-outline-primary btn-lg">
                        <!-- Outputs status of service. -->
                        <p class="status"><?php echo ConvertStatus($visualiserOn) ?></p>
                    </div>
                    <div class="col-sm led">
                        <input type="submit" name="ambilight" value="AmbiLight On/Off" class="btn btn-outline-primary btn-lg">
                        <!-- Outputs status of service. -->
                        <p class="status"><?php echo ConvertStatus($ambilightOn) ?></p>
                    </div>
                    <div class="col-sm led">
                        <input type="submit" name="mood" value="Mood Light On/Off" class="btn btn-outline-primary btn-lg">
                        <!-- Outputs status of service. -->
                        <p class="status mood-status"><?php echo ConvertStatus($moodlightOn) ?></p>
                        <input type="submit" name="apply-colour" value="Apply Colour" class="btn btn-outline-primary btn-lg apply-colour">
                        <div id="color-picker-container"></div>
                        <input type="hidden" name="colour" value="<?php echo $colour ?>" class="colour">
                    </div>
                    <div class="col-sm led">
                        <input type="submit" name="slideshow" value="Slideshow On/Off" class="btn btn-outline-primary btn-lg">
                        <!-- Outputs status of service. -->
                        <p class="status"><?php echo ConvertStatus($slideshowOn) ?></p>
                    </div>
                </div>
                <hr>
               <!--  Media controls. -->
                <div class="row control-name">
                    <h2 class="col-sm">Media Controls</h2>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <input type="submit" name="plex" value="Plex Library Sync" class="btn btn-outline-primary btn-lg">
                    </div>
                </div>
            </div>
        </form>
    </section>

<!-- jQuery. -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<!-- Bootstrap scripts. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<!-- Colour wheel script -->
<script type="text/javascript" src="scripts/iro.min.js"></script>
<!-- Dynamic content script -->
<script type="text/javascript" src="scripts/dynamicContent.js"></script>
<!-- Prefixfree script -->
<script type="text/javascript" src="scripts/prefixfree.min.js"></script>
</body>
</html>
