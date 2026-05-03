<div class="songs index large-9 medium-8 columns content">
    <h3>
        <?= __('Songs') ?>
        <small>
            <?= $this->Html->link('<i class="fi-page-add"></i> ' . __('New'), ['controller' => 'Songs', 'action' => 'add'], ['escape' => false]) ?>
        </small>
        <?php if ($githubEnabled): ?>
        <small class="right">
            <?= $this->Html->link('<i class="fi-refresh"></i> ' . __('Sync'), ['controller' => 'Songs', 'action' => 'sync'], ['escape' => false, 'class' => 'button alert small']) ?>
        </small>
        <?php endif; ?>
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
    <table cellpadding="0" cellspacing="0" class="songs no-padding-on-small">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('Songs.title', 'Title') ?></th>
                <th></th>
                <th class="show-for-medium"><?= $this->Paginator->sort('Songs.created', 'Created') ?></th>
                <th><?= $this->Paginator->sort('Songs.modified', 'Modified') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($songs as $song): ?>
            <tr>
                <td>
                    <?= $this->Html->link($song->title, ['action' => 'view', $song->id]) ?>
                    <?= $song->artist ? ' (' . $song->artist . ')' : '' ?>
                </td>
                <td>
                    <?= $song->url ? '<span class="label success round" title="With exportable chords &amp; lyrics">&#10026;</span>' : '' ?>
                    <?= !empty($song->instructions) ? '<span class="label warning round" title="With instructions">&#10026;</span>' : '' ?>
                    <?= count($song->versions) ? '<span class="label round" title="Has various versions">' . count($song->versions) . '</span>' : '' ?>
                </td>
                <td class="show-for-medium"><?= $this->element('date', ['date' => $song->created]) ?></td>
                <td><?= $this->element('date', ['date' => $song->modified]) ?></td>
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
