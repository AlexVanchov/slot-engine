document.addEventListener('DOMContentLoaded', function() {
    // 10 colors to choose from
    let colors = [
        '#FF0000', // Red
        '#00FF00', // Green
        '#0000FF', // Blue
        '#FFFF00', // Yellow
        '#FF00FF', // Magenta
        '#00FFFF', // Cyan
        '#FFA500', // Orange
        '#800080', // Purple
        '#008000', // Dark Green
        '#000080', // Dark Blue
    ];

    winningLines.forEach(payline => {
        // Generate a random color for each winning line
        const color = colors[Math.floor(Math.random() * colors.length)];
        // remove selected color from the array
        colors = colors.filter(c => c !== color);

        payline.line.forEach((position, index) => {
            const selector = `.position-${index}-${position} span`;
            document.querySelectorAll(selector).forEach(span => {
                // Check if this color has already been added to the span
                if (!span.dataset.colors) {
                    span.dataset.colors = '';
                }
                if (span.dataset.colors.includes(color)) {
                    return; // Skip if color already added
                }

                // Append color to dataset to track it
                span.dataset.colors += `${color};`;

                // Create the color ball
                const ball = document.createElement('div');
                ball.style.width = '10px';
                ball.style.height = '10px';
                ball.style.borderRadius = '50%'; // Makes it circular
                ball.style.backgroundColor = color;
                ball.style.display = 'inline-block';
                ball.style.marginRight = '5px';
                ball.style.position = 'relative';
                ball.style.top = '2px';

                // Add the color ball before the span's content
                span.insertBefore(ball, span.firstChild);
            });
        });
    });
});
document.addEventListener('keydown', function(event) {
    // Check if the pressed key is the space bar (keyCode 32)
    if (event.keyCode === 32) {
        // Prevent the default space bar action (scrolling the page down)
        event.preventDefault();
        // Find the button by its ID and click it
        const spinButton = document.getElementById('btn-spin');
        if (spinButton) {
            spinButton.click();
        }
    }
});

