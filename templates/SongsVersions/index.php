<div class="songsVersions index large-9 medium-8 columns content">
    <h3><?= __('Songs Versions') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('SongVersions.id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('SongVersions.user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('SongVersions.song_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('SongVersions.title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('SongVersions.created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('SongVersions.modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($songsVersions as $songsVersion): ?>
            <tr>
                <td><?= $this->Number->format($songsVersion->id) ?></td>
                <td><?= $songsVersion->hasValue('user') ? $this->Html->link($songsVersion->user->username, ['controller' => 'Users', 'action' => 'view', $songsVersion->user->id]) : '' ?></td>
                <td><?= $songsVersion->hasValue('song') ? $this->Html->link($songsVersion->song->title, ['controller' => 'Songs', 'action' => 'view', $songsVersion->song->id]) : '' ?></td>
                <td><?= h($songsVersion->title) ?></td>
                <td><?= h($songsVersion->created) ?></td>
                <td><?= h($songsVersion->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $songsVersion->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $songsVersion->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $songsVersion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $songsVersion->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
