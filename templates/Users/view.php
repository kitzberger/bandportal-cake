<div class="users view large-9 medium-8 columns content">
    <h3>
        <?= h($user->username) ?>
        <small>
            <?= $this->Html->link(
                '<i class="fi-pencil"></i> ' . __('Edit'),
                ['controller' => 'Users', 'action' => 'edit', $user->id],
                ['escape' => false])
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Username') ?></th>
            <td><?= h($user->username) ?></td>
        </tr>
        <tr>
            <th><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th><?= __('Is Admin') ?></th>
            <td><?= $user->is_admin ? __('Yes') : __('No') ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Comments') ?></h4>
        <?php if (!empty($user->comments)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Record') ?></th>
                <th><?= __('Text') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->comments as $comments): ?>
            <tr>
                <td><?= h($comments->id) ?></td>
                <td>
                    <?= $comments->hasValue('date') ? $this->Html->link($comments->date->title, ['controller' => 'Dates', 'action' => 'view', $comments->date_id]) : '' ?>
                    <?= $comments->hasValue('idea') ? $this->Html->link($comments->idea->title, ['controller' => 'Ideas', 'action' => 'view', $comments->idea_id]) : '' ?>
                    <?= $comments->hasValue('song') ? $this->Html->link($comments->song->title, ['controller' => 'Songs', 'action' => 'view', $comments->song_id]) : '' ?>
                </td>
                <td><?= h($comments->text) ?></td>
                <td><?= h($comments->created) ?></td>
                <td><?= h($comments->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Comments', 'action' => 'view', $comments->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Comments', 'action' => 'edit', $comments->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Comments', 'action' => 'delete', $comments->id], ['confirm' => __('Are you sure you want to delete # {0}?', $comments->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Dates') ?></h4>
        <?php if (!empty($user->dates)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Begin') ?></th>
                <th><?= __('End') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->dates as $dates): ?>
            <tr>
                <td><?= h($dates->id) ?></td>
                <td><?= h($dates->begin) ?></td>
                <td><?= h($dates->end) ?></td>
                <td><?= h($dates->title) ?></td>
                <td><?= h($dates->created) ?></td>
                <td><?= h($dates->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Dates', 'action' => 'view', $dates->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Dates', 'action' => 'edit', $dates->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Dates', 'action' => 'delete', $dates->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dates->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Files') ?></h4>
        <?php if (!empty($user->files)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Record') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('File') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->files as $files): ?>
            <tr>
                <td><?= h($files->id) ?></td>
                <td>
                    <?= $files->hasValue('date') ? $this->Html->link($files->date->title, ['controller' => 'Dates', 'action' => 'view', $files->date_id]) : '' ?>
                    <?= $files->hasValue('idea') ? $this->Html->link($files->idea->title, ['controller' => 'Ideas', 'action' => 'view', $files->idea_id]) : '' ?>
                    <?= $files->hasValue('song') ? $this->Html->link($files->song->title, ['controller' => 'Songs', 'action' => 'view', $files->song_id]) : '' ?>
                </td>
                <td><?= h($files->title) ?></td>
                <td><?= h($files->file) ?></td>
                <td><?= h($files->created) ?></td>
                <td><?= h($files->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Files', 'action' => 'view', $files->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Files', 'action' => 'edit', $files->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Files', 'action' => 'delete', $files->id], ['confirm' => __('Are you sure you want to delete # {0}?', $files->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Ideas') ?></h4>
        <?php if (!empty($user->ideas)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->ideas as $ideas): ?>
            <tr>
                <td><?= h($ideas->id) ?></td>
                <td><?= h($ideas->title) ?></td>
                <td><?= h($ideas->created) ?></td>
                <td><?= h($ideas->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Ideas', 'action' => 'view', $ideas->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Ideas', 'action' => 'edit', $ideas->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Ideas', 'action' => 'delete', $ideas->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ideas->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Collections') ?></h4>
        <?php if (!empty($user->collections)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->collections as $collections): ?>
            <tr>
                <td><?= h($collections->id) ?></td>
                <td><?= h($collections->title) ?></td>
                <td><?= h($collections->created) ?></td>
                <td><?= h($collections->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Collections', 'action' => 'view', $collections->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Collections', 'action' => 'edit', $collections->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Collections', 'action' => 'delete', $collections->id], ['confirm' => __('Are you sure you want to delete # {0}?', $collections->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Votes') ?></h4>
        <?php if (!empty($user->votes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Record') ?></th>
                <th><?= __('Vote') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->votes as $votes): ?>
            <tr>
                <td><?= h($votes->id) ?></td>
                <td>
                    <?= $votes->hasValue('date') ? $this->Html->link($votes->date->title, ['controller' => 'Dates', 'action' => 'view', $votes->date_id]) : '' ?>
                    <?= $votes->hasValue('idea') ? $this->Html->link($votes->idea->title, ['controller' => 'Ideas', 'action' => 'view', $votes->idea_id]) : '' ?>
                </td>
                <td><?= h($votes->vote) ?></td>
                <td><?= h($votes->created) ?></td>
                <td><?= h($votes->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Votes', 'action' => 'view', $votes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Votes', 'action' => 'edit', $votes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Votes', 'action' => 'delete', $votes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $votes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
