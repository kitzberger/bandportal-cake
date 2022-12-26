<div class="songsVersions view large-9 medium-8 columns content">
    <h3>
        <?= h($songsVersion->song->title) . ': ' . h($songsVersion->title) ?>
        <?php $this->assign('title', $songsVersion->title); ?>
        <small>
            <?= $this->Html->link('<i class="fi-pencil"></i> '.__('Edit'), ['controller' => 'SongsVersions', 'action' => 'edit', $songsVersion->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_admin']) {
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
            <td><?= $songsVersion->has('user') ? $this->Html->link($songsVersion->user->username, ['controller' => 'Users', 'action' => 'view', $songsVersion->user->id]) : '' ?></td>
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
            <td><?= $songsVersion->has('song') ? $this->Html->link($songsVersion->song->title, ['controller' => 'Songs', 'action' => 'view', $songsVersion->song->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Length') ?></th>
            <td><?= $this->element('duration', ['seconds' => $songsVersion->length]) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <?= $this->Text->autoParagraph(h($songsVersion->text)); ?>
    </div>
</div>