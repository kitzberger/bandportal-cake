<div class="logs index large-9 medium-8 columns content">
    <h3><?= __('Logs') ?></h3>
    <table cellpadding="0" cellspacing="0" class="logs no-padding-on-small">
        <thead>
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('User') ?></th>
                <th><?= __('Record') ?></th>
                <th><?= __('Relation') ?></th>
                <th><?= __('Diff') ?></th>
                <th><?= $this->Paginator->sort('Logs.created', 'Created') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= $this->Number->format($log->id) ?></td>
                <td><?= $log->hasValue('user') ? $this->element('username', ['user' => $log->user]) : '' ?></td>
                <td>
                    <?php
                        if ($log->hasValue('song')) {
                            echo __('Song') . ': ' . $this->Html->link($log->song->title, ['_full' => true, 'controller' => 'Songs', 'action' => 'view', $log->song->id]);
                        }
                        if ($log->hasValue('songs_version')) {
                            echo __('Song') . ': ' . $this->Html->link($log->songs_version->song->title, ['_full' => true, 'controller' => 'Songs', 'action' => 'view', $log->songs_version->song->id]);
                            echo ', ' . __('Version') . ': ' . $this->Html->link($log->songs_version->title, ['_full' => true, 'controller' => 'SongsVersions', 'action' => 'view', $log->songs_version->id]);
                        }
                        if ($log->hasValue('date')) {
                            echo __('Date on') . ' ' . $log->date->begin->format('Y-m-d') . ': ' . $this->Html->link($log->date->title, ['_full' => true, 'controller' => 'Dates', 'action' => 'view', $log->date->id]);
                        }
                        if ($log->hasValue('idea')) {
                            echo __('Idea') . ': ' . $this->Html->link($log->idea->title, ['_full' => true, 'controller' => 'Ideas', 'action' => 'view', $log->idea->id]);
                        }
                        if ($log->hasValue('collection')) {
                            echo __('Collection') . ': ' . $this->Html->link($log->collection->title, ['controller' => 'Collections', 'action' => 'view', $log->collection->id]);
                        }
                        if ($log->hasValue('share')) {
                            echo __('Share') . ': ' . $this->Html->link($log->share->id, ['_full' => true, 'controller' => 'Shares', 'action' => 'view', $log->share->id]);
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if ($log->hasValue('comment')) {
                            echo __('Comment');
                        }
                        if ($log->hasValue('file')) {
                            echo __('File');
                        }
                    ?>
                </td>
                <td class="diff"><?= $this->Diff->render($log->diff) ?></td>
                <td><?= h($log->created) ?></td>
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
