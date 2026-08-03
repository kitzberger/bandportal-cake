<div class="votes view large-9 medium-8 columns content">
    <h3>
        <?= h($vote->id) ?>
        <small>
            <?= $this->Html->link(
                '<i class="fi-pencil"></i> ' . __('Edit'),
                ['controller' => 'Votes', 'action' => 'edit', $vote->id],
                ['escape' => false])
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $vote->hasValue('user') ? $this->element('username', ['user' => $vote->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Record') ?></th>
            <td>
                <?= $vote->hasValue('date') ? $this->Html->link($vote->date->title, ['controller' => 'Dates', 'action' => 'view', $vote->date->id]) : '' ?>
                <?= $vote->hasValue('idea') ? $this->Html->link($vote->idea->title, ['controller' => 'Ideas', 'action' => 'view', $vote->idea->id]) : '' ?>
            </td>
        </tr>
        <tr>
            <th><?= __('Vote') ?></th>
            <td><?= $this->Number->format($vote->vote) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($vote->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($vote->modified) ?></td>
        </tr>
    </table>
</div>
