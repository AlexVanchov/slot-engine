<!DOCTYPE html>
<?php
// TODO - Layout features
/**
 * @var array $result
 */
?>
<html>
<head>
    <title>Slot Machine</title>
    <link rel="stylesheet" type="text/css" href="/web/css/slot.css">
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

        <input type="number" name="stake" id="stake" value="<?= $_POST['stake'] ?? 1 ?>" step="0.01" min="0.1" max="10">
        <button id="btn-spin">
            Spin
        </button>
    </form>
</div>
<div id="slotMachineScreenResults" class="row">
    <div>
        <h2>Results</h2>
        <?php if (!empty($result['paylines'])): ?>
            <?php
            $totalMoneyWon = 0;
            ?>
            <ul>
                <?php foreach ($result['paylines'] as $payline): ?>
                    <li>
                        Line: <?= implode(', ', $payline['line']) ?>,
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

        <h2>Details</h2>
        <?php if (!empty($result['details'])): ?>
            <ul>
                <?php foreach ($result['details'] as $detail): ?>
                    <li>
                        <?= $detail ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No details</p>
        <?php endif; ?>
    </div>
</div>
<script>
    const winningLines = <?= json_encode($result['paylines']); ?>;
</script>
<script src="/web/js/slot.js"></script>
</body>
</html>
