// Changes status text colour according to on/off value.
$(".status").each(function () {
    if ($(this).text() == "On") {
        $(this).css("color", "Green");
    } else {
        $(this).css("color", "Red");
    }
});

// Disables the Apply Colour button if the mood lights are not enabled.
if($(".mood-status").text() == "On") {
  $(".apply-colour").attr("disabled", false)
} else {
  $(".apply-colour").attr("disabled", true)
}

// Shows/hides the output display according to if it contains content or not.
if ($(".output p").text() == "") {
  $(".output").css("display", "none");
} else {
  $(".output").css("display", "block");
}

// Stores the current mood light colour of the Pi based on hidden input field value.
var currentPiColour = $(".colour").val();

// Creates a Colour wheel.
var demoColorPicker = new iro.ColorPicker("#color-picker-container", {
  height: 255,
  width: 255,
  // Set the initial color to current mood colour used on the Pi.
  color: "rgb(" + currentPiColour + ")"
});

// When the colour is changed carry out the following.
demoColorPicker.on("color:change", function(color, changes) {
  // Variables for storing rgb values of colour wheel.
  var r = "";
  var g = "";
  var b = "";

  // Loop through each value in the colour and store in associated variables.
  for (var key in color.rgb){
    r = color.rgb["r"];
    g = color.rgb["g"];
    b = color.rgb["b"];
  }

  // Concatenate individual rgb values into new variable.
  var rgb = r.toString() + "," + g.toString() + "," + b.toString();
  // Set value of hidden form field to rgb value.
  $(".colour").attr("value", rgb);
});
