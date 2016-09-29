var TABLE1 = (function () {
    var size = {
        start: 2
    };
    var color = {
        start: [0, 0, 0]
    };
    sizeAdd = function (newsize) {
        this.size = newsize;
        return this;
    };
    colorAdd = function (newcolor) {
        this.color = newcolor;
        return this;
    };
    return {
        size: size.start,
        color: color.start,
        sizeAdd: sizeAdd,
        colorAdd: colorAdd,
    };
})();

dependentModule1 = (function (OM) {
// Variables to keep track of the mouse position and left-button status 
    var canvas, ctx;
    var mouseX, mouseY, mouseDown = 0;

// Variables to keep track of the touch position
    var touchX, touchY;


// Draws a dot at a specific position on the supplied canvas name
// Parameters are: A canvas context, the x position, the y position, the size of the dot
    function drawDot(ctx, x, y, size) {
        // Let's use black by setting RGB values to 0, and 255 alpha (completely opaque)
        var mycolor = OM.color;
        r = mycolor[0];
        g = mycolor[1];
        b = mycolor[2];
        a = 255;

        // Select a fill style
        ctx.fillStyle = "rgba(" + r + "," + g + "," + b + "," + (a / 255) + ")";

        // Draw a filled circle
        ctx.beginPath();
        ctx.arc(x, y, size, 0, Math.PI * 2, true);
        ctx.closePath();
        ctx.fill();
    }

// Get the current mouse position relative to the top-left of the canvas
    function getMousePos(e) {
        if (!e)
            var e = event;

        if (e.offsetX) {
            mouseX = e.offsetX;
            mouseY = e.offsetY;
        }
        else if (e.layerX) {
            mouseX = e.layerX;
            mouseY = e.layerY;
        }
    }

// Get the touch position relative to the top-left of the canvas
// When we get the raw values of pageX and pageY below, they take into account the scrolling on the page
// but not the position relative to our target div. We'll adjust them using "target.offsetLeft" and
// "target.offsetTop" to get the correct values in relation to the top left of the canvas.
    function getTouchPos(e) {
        if (!e)
            var e = event;

        if (e.touches) {
            if (e.touches.length == 1) { // Only deal with one finger
                var touch = e.touches[0]; // Get the information for finger #1
                touchX = touch.pageX - touch.target.offsetLeft;
                touchY = touch.pageY - touch.target.offsetTop;
            }
        }
    }

// Keep track of the mouse button being released
    function sketchpad_mouseUp() {
        mouseDown = 0;
    }


// Set-up an individual sketchpad and add our event handlers
// Use the "this" reference to make sure variables and functions are specific to each 
// specific sketchpad created in the "init()" function below.
    function sketchpad(my_sketchpad) {

        // Get the specific canvas element from the HTML document passed
        this.canvas = document.getElementById(my_sketchpad);
        // If the browser supports the canvas tag, get the 2d drawing context for this canvas,
        // and also store it with the canvas as "ctx" for convenience
        if (this.canvas.getContext)
            this.ctx = this.canvas.getContext('2d');


        // Declare some functions associated with a particular sketchpad
        // We will attach these to the canvas as event handlers later
        // Note that the sketcphad_mouseUp function is not included here, since it's not 
        // specific to a certain canvas - we're listening to the entire window for mouseup
        // events.

        // Clear the canvas context using the canvas width and height
        this.clearCanvas = function () {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }

        // Draw something when a touch start is detected
        this.sketchpad_touchStart = function () {
            // Update the touch co-ordinates
            getTouchPos();
            var mysize = OM.size;
            drawDot(this.ctx, touchX, touchY, mysize);

            // Prevents an additional mousedown event being triggered
            event.preventDefault();
        }

        // Draw something and prevent the default scrolling when touch movement is detected
        this.sketchpad_touchMove = function (e) {
            // Update the touch co-ordinates
            getTouchPos(e);
            var mysize = OM.size;
            // During a touchmove event, unlike a mousemove event, we don't need to check if the touch is engaged, since there will always be contact with the screen by definition.
            drawDot(this.ctx, touchX, touchY, mysize);

            // Prevent a scrolling action as a result of this touchmove triggering.
            event.preventDefault();
        }

        // Keep track of the mouse button being pressed and draw a dot at current location
        this.sketchpad_mouseDown = function () {
            mouseDown = 1;
            var mysize = OM.size;
            drawDot(this.ctx, mouseX, mouseY, mysize);
        }

        // Keep track of the mouse position and draw a dot if mouse button is currently pressed
        this.sketchpad_mouseMove = function (e) {
            // Update the mouse co-ordinates when moved
            getMousePos(e);

            // Draw a dot if the mouse button is currently being pressed
            if (mouseDown == 1) {
                var mysize = OM.size;
                drawDot(this.ctx, mouseX, mouseY, mysize);
            }
        }


        // Add event handlers
        // Check that we have a valid context to draw on/with before adding event handlers
        if (this.ctx) {
            // React to mouse events on the canvas, and mouseup on the entire document
            this.canvas.addEventListener('mousedown', this.sketchpad_mouseDown.bind(this), false);
            this.canvas.addEventListener('mousemove', this.sketchpad_mouseMove.bind(this), false);

            // React to touch events on the canvas
            this.canvas.addEventListener('touchstart', this.sketchpad_touchStart.bind(this), false);
            this.canvas.addEventListener('touchmove', this.sketchpad_touchMove.bind(this), false);
        }

    }

// Create two sketchpads when the page loads, using our canvas elements called sketchpad1 and sketchpad2
    function init() {
        sketch1 = new sketchpad('sketchpad1');
        // Since we are listening to the entire window for the mouseup, it only needs to be done once per page,
        // and not once per canvas
        window.addEventListener('mouseup', sketchpad_mouseUp, false);

        var trigger = document.getElementById("clearsmallsketch");
        trigger.addEventListener(
                "click",
                function (event) {
                    event.stopPropagation();
                    sketch1.clearCanvas();
                }
        );
    }
    return {
        init: init,
    };

}(TABLE1));
dependentModule1.init();