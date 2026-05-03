<div class="songs view large-9 medium-8 columns content">
    <h3>
        <?= h($song->title) ?>
        <?php $this->assign('title', $song->title); ?>
        <small class="hide-for-print">
            <?= $this->Html->link('<i class="fi-pencil"></i> ' . __('Edit'), ['controller' => 'Songs', 'action' => 'edit', $song->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_active'] == 1 && $currentUser['id'] == $song->user_id) {
                    echo $this->Form->postLink(
                        '<i class="fi-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', $song->id],
                        ['confirm' => __('Are you sure you want to delete "{0}"?', $song->title), 'escape' => false]
                    );
                }
            ?>
        </small>
        <small class="right hide-for-print">
            <?php
            if ($song->text) {
                echo $this->Html->link(
                    __('Tabs/Chords'),
                    ['controller' => 'Songs', 'action' => 'display', $song->id],
                    ['escape' => false, 'class' => 'button small success']
                );
            }
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $song->has('user') ? $this->element('username', ['user' => $song->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= $this->element('date', ['date' => $song->created]) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= $this->element('date', ['date' => $song->modified]) ?></td>
        </tr>
        <?php if ($song->artist): ?>
        <tr>
            <th><?= __('Artist') ?></th>
            <td><?= $this->Html->link(h($song->artist), ['controller' => 'Songs', 'action' => 'index', 'sword' => $song->artist]) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><?= __('Tabs/Chords') ?> <span class="label success round" title="With exportable chords &amp; lyrics">&#10026;</span></th>
            <td><?php
                if ($song->text) {
                    echo $this->Html->link('<i class="fi-page"></i> ' . urldecode($song->url), ['controller' => 'Songs', 'action' => 'display', $song->id], ['escape' => false]);
                }
                ?>
            </td>
        </tr>
    </table>
    <div class="related">
        <?php if ($song->instructions): ?>
            <h4>
                <?= __('Instructions') ?>
                <span class="label warning round" title="With instructions">&#10026;</span>
            </h4>
            <div class="markdown instructions"><?= $this->Markdown->transform($song->instructions); ?></div>
        </tr>
        <?php endif; ?>

        <h4>
            <?= __('Versions') ?>
            <span class="label round" title="Has various versions"><?= count($song->versions) ?></span>
            <small>
                <?= $this->Html->link(
                    '<i class="fi-page-add"></i> ' . __('New'),
                    [
                        'controller' => 'SongsVersions',
                        'action' => 'add',
                        '?' => [
                            'song_id' => $song->id,
                        ],
                    ],
                    ['escape' => false]
                ); ?>
            </small>
        </h4>
        <?php
            if (count($song->versions)) {
                echo '<table cellpadding="0" cellspacing="0" class="versions">';
                echo '<tr><th>Name</th><th>Length</th><th>Reference file</th><th class="show-for-medium">Modified</th></tr>';
                foreach ($song->versions as $version) {
                    echo '<tr>';
                    echo '<td>' . $this->Html->link($version->title, ['controller' => 'SongsVersions', 'action' => 'view', $version->id]) . '</td>';
                    echo '<td class="no-wrap">' . $this->element('duration', ['seconds' => $version->length]) . '</td>';
                    if (count($version->files)) {
                        echo '<td>';
                        foreach ($version->files as $file) {
                            echo $this->element('Files/embed-inline', ['file' => $file]);
                        }
                        echo '</td>';
                    } else {
                        echo '<td></td>';
                    }
                    echo '<td class="show-for-medium">' . $this->element('date', ['date' => $version->modified]) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '-';
            }
        ?>
    </div>
    <div class="related">
        <h4><?= __('Comments') ?></h4>
        <?= $this->element('Comments/list', ['comments' => $song->comments]); ?>
        <?= $this->element('Comments/new', ['comment' => null, 'song_id' => $song->id]); ?>
    </div>
    <div class="related">
        <h4><?= __('Files') ?></h4>
        <?= $this->element('Files/upload', ['song_id' => $song->id]); ?>
        <?= $this->element('Files/list', ['files' => $song->files]); ?>
    </div>
    <div class="related">
        <h4><?= __('Collections') ?></h4>
        <?= $this->element('Collections/list', ['collections' => $song->collections]) ?>
    </div>
</div>
