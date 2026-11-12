<div class="ideas index large-9 medium-8 columns content">
    <h3>
        <?= __('Ideas') ?>
        <small>
            <?= $this->Html->link('<i class="fi-page-add"></i> ' . __('New'), ['controller' => 'Ideas', 'action' => 'add'], ['escape' => false]) ?>
        </small>
    </h3>
    <?= $this->element('sword', ['sword' => $sword]) ?>
    <p>
        <?=

            $this->Paginator->counter(
                'Page {{page}} of {{pages}}, showing {{current}} records out of
                 {{count}} total, starting on record {{start}}, ending on {{end}}'
            );

        ?>
    </p>
    <table cellpadding="0" cellspacing="0" class="ideas no-padding-on-small">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('Ideas.title', 'Title') ?></th>
                <th class="show-for-medium"><?= $this->Paginator->sort('Ideas.user_id', 'User') ?></th>
                <th class="show-for-medium"><?= $this->Paginator->sort('Ideas.created', 'Created') ?></th>
                <th><?= $this->Paginator->sort('Ideas.modified', 'Modified') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ideas as $idea): ?>
            <tr>
                <td><?= $this->Html->link($idea->title, ['action' => 'view', $idea->id]) ?></td>
                <td class="show-for-medium"><?= $idea->hasValue('user') ? $this->element('username', ['user' => $idea->user]) : '' ?></td>
                <td class="show-for-medium"><?= $this->element('date', ['date' => $idea->created]) ?></td>
                <td><?= $this->element('date', ['date' => $idea->modified]) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($this->Paginator->total() > 1): ?>
        <div class="paginator">
            <ul class="pagination">
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
            </ul>
            <p><?= $this->Paginator->counter() ?></p>
        </div>
    <?php endif; ?>
</div>
