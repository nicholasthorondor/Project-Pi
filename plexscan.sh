#!/bin/bash

#Plex token (unique to system)
#Bgdt3fB1qHrvPJiUfD37

# Cleanly exits on an error
set -e

# Refreshes the movies library (id 1) using url
curl localhost:32400/library/sections/1/refresh?X-Plex-Token=Bgdt3fB1qHrvPJiUfD37 > /dev/null 2>&1 &

# Refreshes the TV library (id 2) using url
curl localhost:32400/library/sections/2/refresh?X-Plex-Token=Bgdt3fB1qHrvPJiUfD37 > /dev/null 2>&1 &
