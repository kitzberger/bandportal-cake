<div class="logs view large-9 medium-8 columns content">
    <h3><?= h($log->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $log->hasValue('user') ? $this->element('username', ['user' => $log->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Song') ?></th>
            <td><?= $log->hasValue('song') ? $this->Html->link($log->song->title, ['controller' => 'Songs', 'action' => 'view', $log->song->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Song version') ?></th>
            <td><?= $log->hasValue('songs_version') ? $this->Html->link($log->songs_version->title, ['controller' => 'SongsVersions', 'action' => 'view', $log->songs_version->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Date') ?></th>
            <td><?= $log->hasValue('date') ? $this->Html->link($log->date->title, ['controller' => 'Dates', 'action' => 'view', $log->date->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Idea') ?></th>
            <td><?= $log->hasValue('idea') ? $this->Html->link($log->idea->title, ['controller' => 'Ideas', 'action' => 'view', $log->idea->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Comment') ?></th>
            <td><?= $log->hasValue('comment') ? $this->Html->link($log->comment->id, ['controller' => 'Comments', 'action' => 'view', $log->comment->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Collection') ?></th>
            <td><?= $log->hasValue('collection') ? $this->Html->link($log->collection->title, ['controller' => 'Collections', 'action' => 'view', $log->collection->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Vote') ?></th>
            <td><?= $log->hasValue('vote') ? $this->Html->link($log->vote->id, ['controller' => 'Votes', 'action' => 'view', $log->vote->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('File') ?></th>
            <td><?= $log->hasValue('file') ? $this->Html->link($log->file->title, ['controller' => 'Files', 'action' => 'view', $log->file->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($log->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($log->created) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Diff') ?></h4>
        <?= $this->Text->autoParagraph(h($log->diff)); ?>
    </div>
</div>
