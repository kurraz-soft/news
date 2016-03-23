<?php
/**
 * @var \app\models\ForexBalanceRow[] $rows
 */
?>

<table class="table table-bordered table-striped">
    <tr>
        <th>Name</th>
        <th>Invests</th>
        <th>Current Balance</th>
        <th>Type</th>
    </tr>
    <?php foreach($rows as $row): ?>
        <tr>
            <td><?= $row->name ?></td>
            <td><?= $row->invests ?></td>
            <td><?= $row->balance ?></td>
            <td><?= $row->type ?></td>
        </tr>
    <?php endforeach ?>
</table>