<div class="votes index large-9 medium-8 columns content">
    <h3>
        <?= __('Votes') ?>
        <small>
            <?= $this->Html->link('<i class="fi-page-add"></i> ' . __('New'), ['controller' => 'Votes', 'action' => 'add'], ['escape' => false]) ?>
        </small>
    </h3>
    <p>
        <?=

            $this->Paginator->counter(
                'Page {{page}} of {{pages}}, showing {{current}} records out of
                 {{count}} total, starting on record {{start}}, ending on {{end}}'
            );

        ?>
    </p>
    <table cellpadding="0" cellspacing="0" class="votes no-padding-on-small">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('Votes.user_id', 'User') ?></th>
                <th><?= __('Record') ?></th>
                <th><?= $this->Paginator->sort('Votes.vote', 'Vote') ?></th>
                <th><?= $this->Paginator->sort('Votes.created', 'Created') ?></th>
                <th><?= $this->Paginator->sort('Votes.modified', 'Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($votes as $vote): ?>
            <tr>
                <td><?= $vote->hasValue('user') ? $this->element('username', ['user' => $vote->user]) : '' ?></td>
                <td>
                    <?= $vote->hasValue('date') ? __('Date') . ': ' . $this->Html->link($vote->date->title, ['controller' => 'Dates', 'action' => 'view', $vote->date->id]) : '' ?>
                    <?= $vote->hasValue('idea') ? __('Idea') . ': ' . $this->Html->link($vote->idea->title, ['controller' => 'Ideas', 'action' => 'view', $vote->idea->id]) : '' ?>
                </td>
                <td><?= $this->Number->format($vote->vote) ?></td>
                <td><?= $this->element('date', ['date' => $vote->created]) ?></td>
                <td><?= $this->element('date', ['date' => $vote->modified]) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $vote->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $vote->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $vote->id], ['confirm' => __('Are you sure you want to delete # {0}?', $vote->id)]) ?>
                </td>
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
