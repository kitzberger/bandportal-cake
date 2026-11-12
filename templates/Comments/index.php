<div class="comments index large-9 medium-8 columns content">
    <h3>
        <?= __('Comments') ?>
        <small>
            <?= $this->Html->link('<i class="fi-page-add"></i> ' . __('New'), ['controller' => 'Comments', 'action' => 'add'], ['escape' => false]) ?>
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
    <table cellpadding="0" cellspacing="0" class="comments no-padding-on-small">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('Comments.user_id', 'User') ?></th>
                <th><?= __('Record') ?></th>
                <th><?= $this->Paginator->sort('Comments.created', 'Created') ?></th>
                <th><?= $this->Paginator->sort('Comments.modified', 'Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?= $comment->hasValue('user') ? $this->element('username', ['user' => $comment->user]) : '' ?></td>
                <td>
                    <?= $comment->hasValue('date') ? __('Date') . ': ' . $this->Html->link($comment->date->title, ['controller' => 'Dates', 'action' => 'view', $comment->date->id]) : '' ?>
                    <?= $comment->hasValue('idea') ? __('Idea') . ': ' . $this->Html->link($comment->idea->title, ['controller' => 'Ideas', 'action' => 'view', $comment->idea->id]) : '' ?>
                    <?= $comment->hasValue('song') ? __('Song') . ': ' . $this->Html->link($comment->song->title, ['controller' => 'Songs', 'action' => 'view', $comment->song->id]) : '' ?>
                    <?= $comment->hasValue('songs_version') ? __('Song version') . ': ' . $this->Html->link($comment->songs_version->title, ['controller' => 'SongsVersions', 'action' => 'view', $comment->songs_version->id]) : '' ?>
                    <?= $comment->hasValue('collection') ? __('Collection') . ': ' . $this->Html->link($comment->collection->title, ['controller' => 'Collections', 'action' => 'view', $comment->collection->id]) : '' ?>
                </td>
                <td><?= $this->element('date', ['date' => $comment->created]) ?></td>
                <td><?= $this->element('date', ['date' => $comment->modified]) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $comment->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $comment->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $comment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $comment->id)]) ?>
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
