<div class="songsVersions view large-9 medium-8 columns content">
    <h3>
        <?= h($songsVersion->song->title) . ': ' . h($songsVersion->title) ?>
        <?php $this->assign('title', $songsVersion->title); ?>
        <small class="hide-for-print">
            <?= $this->Html->link('<i class="fi-pencil"></i> '.__('Edit'), ['controller' => 'SongsVersions', 'action' => 'edit', $songsVersion->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_active'] == 1 && $currentUser['id'] == $songsVersion->user_id) {
                    echo $this->Form->postLink(
                        '<i class="fi-trash"></i> '.__('Delete'),
                        ['action' => 'delete', $songsVersion->id],
                        ['confirm' => __('Are you sure you want to delete "{0}"?', $songsVersion->title), 'escape' => false]
                    );
                }
            ?>
        </small>
        <small class="right">
            <?php
            /*if ($songsVersion->text) {
                echo $this->Html->link(
                    __('Tabs/Chords'),
                    ['controller' => 'Songs', 'action' => 'display', $song->id],
                    ['escape' => false, 'class' => 'button small success']
                );
            }*/
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $songsVersion->hasValue('user') ? $this->Html->link($songsVersion->user->username, ['controller' => 'Users', 'action' => 'view', $songsVersion->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= $this->element('date', ['date' => $songsVersion->created]) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= $this->element('date', ['date' => $songsVersion->modified]) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Song') ?></th>
            <td><?= $songsVersion->hasValue('song') ? $this->Html->link($songsVersion->song->title, ['controller' => 'Songs', 'action' => 'view', $songsVersion->song->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Length') ?></th>
            <td><?= $this->element('duration', ['seconds' => $songsVersion->length]) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <?php if ($songsVersion->text) { ?>
            <div class="markdown mode-full"><?= $this->Markdown->transform($songsVersion->text) ?></div>
        <?php } ?>
    </div>
    <div class="related">
        <h4><?= __('Comments') ?></h4>
        <?= $this->element('Comments/list', ['comments' => $songsVersion->comments]); ?>
        <?= $this->element('Comments/new', ['comment' => null, 'song_version_id' => $songsVersion->id]); ?>
    </div>
    <div class="related">
        <h4><?= __('Files') ?></h4>
        <?= $this->element('Files/upload', ['song_version_id' => $songsVersion->id]); ?>
        <?= $this->element('Files/list', ['files' => $songsVersion->files]); ?>
    </div>
</div>
