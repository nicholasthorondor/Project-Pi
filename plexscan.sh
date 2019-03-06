#!/bin/bash

#Plex token (unique to system)
#Egdt3fB1qHpvPJiUfD34

# Cleanly exits on an error
set -e

# Refreshes the movies library (id 1) using url
curl localhost:32400/library/sections/1/refresh?X-Plex-Token=Egdt3fB1qHpvPJiUfD34 > /dev/null 2>&1 &

# Refreshes the TV library (id 2) url
curl localhost:32400/library/sections/2/refresh?X-Plex-Token=Egdt3fB1qHpvPJiUfD34 > /dev/null 2>&1 &
