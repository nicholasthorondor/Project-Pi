#!/usr/bin/env python3

import time
from neopixel import *
import sys

# LED strip configuration:
LED_COUNT      = 84      # Number of LED pixels.
LED_PIN        = 18      # GPIO pin connected to the pixels (18 uses PWM!).
#LED_PIN        = 10      # GPIO pin connected to the pixels (10 uses SPI /dev/spidev0.0).
LED_FREQ_HZ    = 800000  # LED signal frequency in hertz (usually 800khz)
LED_DMA        = 10      # DMA channel to use for generating signal (try 10)
LED_BRIGHTNESS = 255     # Set to 0 for darkest and 255 for brightest
LED_INVERT     = False   # True to invert the signal (when using NPN transistor level shift)
LED_CHANNEL    = 0       # set to '1' for GPIOs 13, 19, 41, 45 or 53

# Create NeoPixel object with appropriate configuration.
strip = Adafruit_NeoPixel(LED_COUNT, LED_PIN, LED_FREQ_HZ, LED_DMA, LED_INVERT, LED_BRIGHTNESS, LED_CHANNEL)
# Intialize the library (must be called once before other functions).
strip.begin()
# Grabs the rgb parametres passed in as arguments to the script.
r = int(sys.argv[1])
g = int(sys.argv[2])
b = int(sys.argv[3])
# Sets the LEDs colour to this rgb value by looping through each light.
while True:
    for i in range(LED_COUNT):
        strip.setPixelColor(i, Color(r,g,b))
    # Applies the colour and displays.
    strip.show()
# Exits script cleanly.
#quit()
