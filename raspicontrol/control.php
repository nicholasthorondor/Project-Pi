<?php 
    // Starts a session.
    session_start();

    //-------------
    //VARIABLES
    //-------------
    // Check status of each required service according to session variable, and assign to associated variable.
    $visualiserOn = $_SESSION["visualiserOn"];
    $ambilightOn = $_SESSION["ambilightOn"];
    $moodlightOn = $_SESSION["moodlightOn"];
    $slideshowOn = $_SESSION["slideshowOn"];

    // Output variable to store result of control execution.
    $output = "";

    //-------------
    //CONTROL LOGIC
    //-------------
    // Shutdown the Pi if shutdown button is clicked.
    if (isset($_POST["shutdown"])) {
        // Turns off the music visualiser, mood light, ambilight or slideshow if they are active.
        if ($visualiserOn) {
            TurnOffMusicVisualiser();
        }
	if ($ambilightOn) {
	    TurnOffAmbiLight();
	}
	if ($slideshowOn) {
	    TurnOffSlideshow();
	}
	if ($moodlightOn) {
	    TurnOffMoodLights();
	}

	ClearLeds();
        exec("sudo /sbin/shutdown -h now");
        exit("Shutting down Pi");
    }

    // Reboot the Pi if reboot button is clicked.
    if (isset($_POST["reboot"])) {
        // Turns off the music visualiser, mood light, ambilight or slideshow if they are active.
        if ($visualiserOn) {
            TurnOffMusicVisualiser();
        }
	if ($moodlightOn) {
	    TurnOffMoodLights();
	}
	if ($ambilightOn) {
	    TurnOffAmbiLight();
	}
	if ($slideshowOn) {
	    TurnOffSlideshow();
	}

	ClearLeds();
        exec("sudo /sbin/reboot");
        exit("Rebooting Pi");
    }

    // Sync the Plex libraries if plex button is clicked.
    if (isset($_POST["plex"])) {
        exec("/home/pi/scripts/plexscan.sh");
        $output = "Plex libraries syncing";
    }

    // Turn on/off the music visualiser if music visualiser button is clicked.
    if (isset($_POST["visualiser"])) {
        // Checks to see the visualiser software status and turns on/off accordingly, turning off other software where appropriate.
        if (!$visualiserOn) {
            // Turns off the mood light, ambilight or slideshow if they are active.
            if ($moodlightOn) {
                TurnOffMoodLights();
            }
	    if ($ambilightOn) {
	        TurnOffAmbiLight();
	    }
	    if ($slideshowOn) {
	        TurnOffSlideshow();
	    }
            // Starts the music visualiser software in the background.
            exec("sudo /usr/bin/python /home/pi/music_visualiser/python/visualization.py > /dev/null &");
            $output = "Music visualiser activated";
        } else {
            TurnOffMusicVisualiser();
	    $output = "Music visualiser deactivated";
        }
    }

    // Turn on/off the ambilight if ambilight button is clicked.
    if (isset($_POST["ambilight"])) {
	if (!$ambilightOn) {
            // Turns off the music visualiser, mood light or slideshow if they are active.
            if ($visualiserOn) {
                TurnOffMusicVisualiser();
            }
    	    if ($moodlightOn) {
    	        TurnOffMoodLights();
    	    }
    	    if ($slideshowOn) {
    	        TurnOffSlideshow();
    	    }
	    // If ambilight is not on, turn on.
	   exec("sudo /bin/systemctl start hyperion.service");
	   $output = "Ambilight activated.";
	} else {
	    // If ambilight is on, turn off.
	    TurnOffAmbiLight();
	    $output = "Ambilight deactivated.";
	}
    }

    // Turn on/off the mood lights if mood lights button is clicked.
    if (isset($_POST["mood"])) {
        // Fetches the colour wheels value by individual rgb.
        $colour = explode(",", $_POST["colour"]);
        $red = $colour[0];
        $green = $colour[1];
        $blue = $colour[2];
	// Saves the colour selection to file for new default value on startup.
	SaveColour($red, $green, $blue);
        if (!$moodlightOn) {
            // Turns off the music visualiser, ambilight or slideshow if they are active.
            if ($visualiserOn) {
                TurnOffMusicVisualiser();
            }
    	    if ($ambilightOn) {
    		TurnOffAmbiLight();
    	    }
    	    if ($slideshowOn) {
    		TurnOffSlideshow();
    	    }
            // If mood light is off, turn on with the colour chosen from the colour wheel.
            exec("sudo /usr/bin/python /home/pi/scripts/mood_light_colour.py $green $red $blue > /dev/null &");
            $output = "Mood lighting activated";
        } else {
            TurnOffMoodLights();
	    $output = "Mood lighting deactivated";
        }
    }

    // Apply new colour to mood lights.
    if (isset($_POST["apply-colour"])) {
        if ($moodlightOn) {
            // If the mood lights are on fetches the colour wheels value by individual rgb.
            $colour = explode(",", $_POST["colour"]);
            $red = $colour[0];
            $green = $colour[1];
            $blue = $colour[2];
	    // Saves colour selection to file for new default value on startup.
	    SaveColour($red, $green, $blue);
	    // Kills the current mood light process.
	    TurnOffMoodLights();
            // Activates new colour on the LEDs.
            exec("sudo /usr/bin/python /home/pi/scripts/mood_light_colour.py $green $red $blue > /dev/null &");
        }
    }

    // Turn on/off the slideshow mode if slideshow button is clicked.
    if (isset($_POST["slideshow"])) {
	if (!$slideshowOn) {
            // Turns off the music visualiser and mood light if they are active.
            if ($visualiserOn) {
                TurnOffMusicVisualiser();
            }
    	    if ($moodlightOn) {
    	        TurnOffMoodLights();
    	    }
	    // If ambilight is not on, turn on.
	    if (!$ambilightOn) {
	        exec("sudo /bin/systemctl start hyperion.service");
	    }
	    // If the slideshow is not on, turn on via custom script.
	   exec("/home/pi/scripts/slideshow.sh");
	   $output = "Slideshow activated.";
	} else {
	    // If the slideshow is on, turn off.
	    TurnOffSlideshow();
	    $output = "Slideshow deactivated.";
	}
    }

    // Stores output in a session variable.
    $_SESSION["output"] = $output;
    // Redirects to the index page once control processing has finished.
    header("Location: index.php");

    // Turn off the music visualisation software.
    function TurnOffMusicVisualiser () {
        // Get the process id of the process running under the visualization.py name.
        $pid = shell_exec("/bin/pidof python virtualization.py");
        // Use the process id to terminate the running virtualisation process.
        exec ("sudo /bin/kill $pid");
        // Clears the LEDs of colour using edited script of the rpi_ws281x libraries.
        ClearLeds();
    }

    // Turn off the mood lights.
    function TurnOffMoodLights () {
        // Get the process id of the process running under the mood_light_colour.py name.
        $pid = shell_exec("/bin/pidof python mood_light_colour.py");
        // Use the process id to terminate the running mood light process.
        exec ("sudo /bin/kill $pid");
        // Clears the LEDs of colour.
        ClearLeds();
    }

    // Turn off the AmbiLight software.
    function TurnOffAmbiLight () {
	// Turns off AmbiLight by stopping the hyperion service.
	exec("sudo /bin/systemctl stop hyperion.service");
    }

    function TurnOffSlideshow () {
	// Get the process id of the process running under the fbi name.
        $pid = shell_exec("/bin/pidof fbi");
        // Use the process id to terminate the running slideshow (fbi) process.
        exec ("sudo /bin/kill $pid");
	// Turns off ambilight.
	//TurnOffAmbiLight();
    }

    // Save colour selection to file for default startup colour.
    function SaveColour ($red, $green, $blue) {
        $colourfile = fopen ("/var/www/html/raspicontrol/mood_light_last_colour.txt", "w") or die ("Unable to open file.");
        $rgb = $red . "\n" . $green . "\n" . $blue . "\n";
        fwrite ($colourfile, $rgb);
        fclose ();
    }

    function ClearLeds () {
	exec ("sudo /usr/bin/python /home/pi/scripts/led_clear.py");
    }
?>
