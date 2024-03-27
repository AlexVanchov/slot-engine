<!DOCTYPE html>
<html>
<head>
    <title>Slot Machine</title>
<!--    <link rel="stylesheet" href="/web/css/slotMachine.css">-->
<!--    css-->
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }

        .row {
            display: flex;
            justify-content: center;
        }

        .reel {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 10px;
        }

        .reel span {
            font-size: 1.5em;
            font-weight: bold;
        }

        .reel img {
            width: 100px;
            height: 100px;
        }

        #slotMachineScreen {
            margin-top: 20px;
        }

        #btn-spin {
            margin-top: 20px;
            font-size: 1.5em;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

    </style>
</head>
<body>
<h1>Slot Machine</h1>
<div id="slotMachineContainer" style="position: relative;">
    <div id="slotMachineScreen" class="row">
        <?php foreach ($result['screen'] as $reelIndex => $reel): ?>
            <div class="reel col col-2">
                <?php foreach ($reel as $rowIndex => $symbol): ?>
                    <div class="symbol position-<?= $reelIndex ?>-<?= $rowIndex ?>">
                        <span><?= $symbol->id ?></span>
                        <img src="/web/images/slot/<?= $symbol->id ?>.png" alt="Symbol">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <svg id="paylineOverlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></svg>

</div>
<div class="row">
    <form action="/spin" method="POST">

        <input type="number" name="stake" id="stake" value="<?= $_POST['stake'] ?? 1 ?>" step="0.01">
        <button id="btn-spin">
            Spin
        </button>
    </form>
</div>
<div id="slotMachineScreenResults" class="row">
<?php
        ?>

        <div>
            <h2>Results</h2>
            <?php if (!empty($result['paylines'])): ?>
                <?php
                $totalMoneyWon = 0;
                ?>
                <ul>
                    <?php foreach ($result['paylines'] as $payline): ?>
                        <li>
                            Line: <?= implode(', ', $payline['line']->getPositions()) ?>,
                            Matches: <?= implode(', ', $payline['matches']) ?>,
                            Count: <?= $payline['count'] ?>
                            Money Won: <?= $payline['moneyWon'] ?>$
                        </li>
                        <?php
                        $totalMoneyWon += $payline['moneyWon'];
                        ?>
                    <?php endforeach; ?>
                </ul>
                <p>Total money won: <?= $totalMoneyWon ?>$</p>
            <?php else: ?>
                <p>No paylines won</p>
            <?php endif; ?>
        </div>
    </div>
<script>
    // TODO IN JS
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
        const winningLines = <?= json_encode($result['paylines']); ?>;
        winningLines.forEach(line => {
            // Generate a random color for each winning line
            const color = colors[Math.floor(Math.random() * colors.length)];
            // remove selected color from the array
            colors = colors.filter(c => c !== color);

            line.line.positions.forEach((position, index) => {
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


</script>
</body>
</html>
