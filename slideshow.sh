#!/bin/bash

set -e

# The directory containing the slideshow images.
DIR=/home/pi/Pictures/slideshow
# Timer in seconds for how long each image will remain before changing.
timer=30
# Change into the slideshow image directory.
cd $DIR

# Starts the fbi image viewer with options to display images in the specified directory on a timed slideshow.
# The -T 2 option is needed to output the display to hdmi.
sudo fbi -T 2 -noverbose -a -u --blend 1500 -t $timer $DIR/*
