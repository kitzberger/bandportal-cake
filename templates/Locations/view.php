<div class="locations view large-9 medium-8 columns content">
    <h3>
        <?= h($location->title) ?>
        <?php $this->assign('title', $location->title); ?>
        <small>
            <?= $this->Html->link('<i class="fi-pencil"></i> ' . __('Edit'), ['action' => 'edit', $location->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_active'] == 1 && $currentUser['id'] == $location->user_id) {
                    echo $this->Form->postLink(
                        '<i class="fi-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', $location->id],
                        ['confirm' => __('Are you sure you want to delete "{0}"?', $location->title), 'escape' => false]
                    );
                }
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $location->hasValue('user') ? $this->element('username', ['user' => $location->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Url') ?></th>
            <td><?= h($location->url) ?></td>
        </tr>
        <tr>
            <th><?= __('Email') ?></th>
            <td><?= h($location->email) ?></td>
        </tr>
        <tr>
            <th><?= __('Address') ?></th>
            <td><?= h($location->address) ?></td>
        </tr>
        <tr>
            <th><?= __('City') ?></th>
            <td><?= h($location->city) ?></td>
        </tr>
        <tr>
            <th><?= __('Zip') ?></th>
            <td><?= h($location->zip) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($location->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($location->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <?= $this->Text->autoParagraph(h($location->text)); ?>
    </div>
</div>
