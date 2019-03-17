#!/bin/bash

set -e

# Stops the fbi process by finding its process id, then piping that to kill.
sudo pgrep fbi | kill
