<div class="comments view large-9 medium-8 columns content">
    <h3>
        <?= h($comment->id) ?>
        <small>
            <?= $this->Html->link(
                '<i class="fi-pencil"></i> ' . __('Edit'),
                ['controller' => 'Comments', 'action' => 'edit', $comment->id],
                ['escape' => false])
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $comment->hasValue('user') ? $this->element('username', ['user' => $comment->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Record') ?></th>
            <td>
                <?= $comment->hasValue('date') ? $this->Html->link($comment->date->title, ['controller' => 'Dates', 'action' => 'view', $comment->date->id]) : '' ?>
                <?= $comment->hasValue('idea') ? $this->Html->link($comment->idea->title, ['controller' => 'Ideas', 'action' => 'view', $comment->idea->id]) : '' ?>
                <?= $comment->hasValue('song') ? $this->Html->link($comment->song->title, ['controller' => 'Songs', 'action' => 'view', $comment->song->id]) : '' ?>
                <?= $comment->hasValue('songs_version') ? $this->Html->link($comment->songs_version->title, ['controller' => 'SongsVersions', 'action' => 'view', $comment->songs_version->id]) : '' ?>
                <?= $comment->hasValue('collection') ? $this->Html->link($comment->collection->title, ['controller' => 'Collections', 'action' => 'view', $comment->collection->id]) : '' ?>
            </td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($comment->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= ($comment->created != $comment->modified) ? h($comment->modified) : '' ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <pre><?= h($comment->text, false); ?>
        </pre>
    </div>
</div>
