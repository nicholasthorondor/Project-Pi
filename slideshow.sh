#!/bin/bash

set -e

# The directory containing the slideshow images.
DIR=/home/pi/Pictures/slideshow
# Creates and empty array to hold images.
#array=()
# Timer in seconds for how long each image will remain before changing.
timer=30
# Change into the slideshow image directory.
cd $DIR

# Starts the fbi image viewer with options to display images in the specified directory on a timed slideshow.
# The -T 2 option is needed to output the display to hdmi.
sudo fbi -T 2 -noverbose -a -u --blend 1500 -t $timer $DIR/*

# Count the number of images inside the directory.
#count=$(find /home/pi/Pictures/slideshow/ -maxdepth 1 -not -type d | wc -l)

# Loops through all jpg and and images in the slideshow directory and stores in an array.
#for file in $DIR/*.{jpg,png}
    #do
    #array+=("$file")
    #done

# Generates a random number between 0 and the count of images - 1.
#random=$(($RANDOM % $count))
# Stores the previous image number, in this first case it will be what random initially calculates.
#previous=$random
