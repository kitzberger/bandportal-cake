<div class="files view large-9 medium-8 columns content">
    <h3>
        <?= h($file->title) ?>
        <?php $this->assign('title', $file->title); ?>
        <small>
            <?= $this->Html->link('<i class="fi-pencil"></i> ' . __('Edit'), ['controller' => 'Files', 'action' => 'edit', $file->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_active'] == 1 && $currentUser['id'] == $file->user_id) {
                    echo $this->Form->postLink(
                        '<i class="fi-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', $file->id],
                        ['confirm' => __('Are you sure you want to delete "{0}"?', $file->title), 'escape' => false]
                    );
                }
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('File') ?></th>
            <td>
                <?= $this->Html->link($file->file, 'uploads' . DS . $file->file) ?>
            </td>
        </tr>
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $file->hasValue('user') ? $this->element('username', ['user' => $file->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Reference') ?></th>
            <td><?= $file->is_public ? '<i class="fi-check"></i>' : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Record') ?></th>
            <td>
                <?= $file->hasValue('date') ? __('Date') . ': ' . $this->Html->link($file->date->title, ['controller' => 'Dates', 'action' => 'view', $file->date->id]) . '<br>' : '' ?>
                <?= $file->hasValue('idea') ? __('Idea') . ': ' . $this->Html->link($file->idea->title, ['controller' => 'Ideas', 'action' => 'view', $file->idea->id]) . '<br>' : '' ?>
                <?= $file->hasValue('song') ? __('Song') . ': ' . $this->Html->link($file->song->title, ['controller' => 'Songs', 'action' => 'view', $file->song->id]) : '' ?>
                <?= $file->hasValue('songs_version') ? __('Song version') . ': ' . $this->Html->link($file->songs_version->title, ['controller' => 'SongsVersions', 'action' => 'view', $file->songs_version->id]) : '' ?>
            </td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($file->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($file->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('File') ?></h4>
        <?= $this->element('Files/embed', ['file' => $file]) ?>

        <h4><?= __('Regions') ?></h4>
        <?php if ($file->regions): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Title</th>
                </tr>
                <?php foreach (json_decode($file->regions, false) as $region): ?>
                    <tr>
                        <td><?= h($region->id) ?></td>
                        <td><?= number_format($region->start, 1) ?></td>
                        <td><?= number_format($region->end, 1) ?></td>
                        <td><?= h($region->title) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Collections') ?></h4>
        <?= $this->element('Collections/list', ['collections' => $file->collections]) ?>
    </div>
</div>
