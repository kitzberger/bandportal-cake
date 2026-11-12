<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Share $share
 */
?>
<div class="shares view large-9 medium-8 columns content">
    <h3>
        <?= h($share->id) ?>
        <small>
            <?= $this->Html->link(__('Edit'), ['controller' => 'Shares', 'action' => 'edit', $share->id]) ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $share->hasValue('user') ? $this->element('username', ['user' => $share->user]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Record') ?></th>
            <td>
                <?= $share->hasValue('date') ? $this->Html->link($share->date->title, ['controller' => 'Dates', 'action' => 'view', $share->date->id]) : '' ?>
                <?= $share->hasValue('idea') ? $this->Html->link($share->idea->title, ['controller' => 'Ideas', 'action' => 'view', $share->idea->id]) : '' ?>
                <?= $share->hasValue('song') ? $this->Html->link($share->song->title, ['controller' => 'Songs', 'action' => 'view', $share->song->id]) : '' ?>
                <?= $share->hasValue('collection') ? $this->Html->link($share->collection->title, ['controller' => 'Collections', 'action' => 'view', $share->collection->id]) : '' ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= __('Expire Date') ?></th>
            <td><?= h($share->expire_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($share->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($share->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Comment') ?></h4>
        <?= $this->Text->autoParagraph(h($share->comment)); ?>
    </div>
</div>
