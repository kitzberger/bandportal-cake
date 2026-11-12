<div class="dates view large-9 medium-8 columns content">
    <h3>
        <?= h($date->title) ?>
        <?php $this->assign('title', $date->title); ?>
        <small>
            <?= $this->Html->link('<i class="fi-pencil"></i> ' . __('Edit'), ['controller' => 'Dates', 'action' => 'edit', $date->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_active'] == 1 && $currentUser['id'] == $date->user_id) {
                    echo $this->Form->postLink(
                        '<i class="fi-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', $date->id],
                        ['confirm' => __('Are you sure you want to delete "{0}"?', $date->title), 'escape' => false]
                    );
                }
                if ($remoteCalendarEnabled) {
                    echo ' ';
                    echo $this->Html->link('<i class="fi-upload-cloud"></i> ' . __('Publish'), ['controller' => 'Dates', 'action' => 'publish', $date->id], ['escape' => false]);
                    if ($date->uri) {
                        echo ' ';
                        echo $this->Html->link('<i class="fi-trash"></i> ' . __('Unpublish'), ['controller' => 'Dates', 'action' => 'unpublish', $date->id], ['escape' => false]);
                    }
                }
            ?>
        </small>
        <small class="right">
            <?php
                echo $this->element('Button/ShareLink', ['passiveUsers' => $passiveUsers, 'controller' => 'Dates', 'record' => $date]);
            ?>
        </small>
    </h3>

    <?php
        if ($collidingDates->count()) {
            echo '<div data-alert class="alert-box warning">';
            echo '<p>' . __('Behold! There\'s other events on that day!') . '</p>';
            echo '<ul>';
            foreach ($collidingDates as $collidingDate) {
                echo '<li>' . $collidingDate->user->username . '\'s ' . $this->Html->link($collidingDate->title, ['controller' => 'Dates', 'action' => 'view', $collidingDate->id]) . '</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    ?>

    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $date->hasValue('user') ? $this->element('username', ['user' => $date->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Location') ?></th>
            <td><?= $date->hasValue('location') ? $this->Html->link($date->location->title, ['controller' => 'Locations', 'action' => 'view', $date->location->id]) : '' ?></td>
        </tr>
        <?php
            if ($date->is_fullday) {
                $dateFormat = 'D, d.m.Y';
            } else {
                $dateFormat = 'D, d.m.Y H:i';
            }
        ?>
        <tr>
            <th><?= __('Begin') ?></th>
            <td><?= h($date->begin->format($dateFormat)) ?></td>
        </tr>
        <tr>
            <th><?= __('End') ?></th>
            <td><?= $date->end ? h($date->end->format($dateFormat)) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $this->element('Dates/status') ?></td>
        </tr>
        <?php if ($remoteCalendarEnabled): ?>
        <tr>
            <th><?= __('Calendar entry') ?></th>
            <td><?= $date->uri ? $this->Html->link($date->uri, ['controller' => 'Dates', 'action' => 'download', $date->id]) : '' ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($date->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($date->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Shared with') ?></th>
            <td><?= $this->element('shares', ['shares' => $date->shares]) ?></td>
        </tr>
    </table>
    <?php if ($date->text): ?>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <?= $this->element('text', ['text' => $date->text, 'markdown' => true]) ?>
    </div>
    <?php endif ?>
    <div class="related">
        <h4><?= __('Votes') ?></h4>
        <table>
            <tr>
                <th><?= __('Active') ?></th>
                <th><?= __('Passive') ?></th>
            </tr>
            <tr>
                <td>
                    <?= $this->element('votes', ['record' => $date, 'users' => $users]) ?>
                </td>
                <td>
                    <?= $this->element('votes', ['record' => $date, 'users' => $passiveUsers]) ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Comments') ?></h4>
        <?= $this->element('Comments/list', ['comments' => $date->comments]); ?>
        <?= $this->element('Comments/new', ['comment' => null, 'date_id' => $date->id]); ?>
    </div>
    <div class="related">
        <h4><?= __('Files') ?></h4>
        <?= $this->element('Files/list', ['files' => $date->files]); ?>
        <?= $this->element('Files/upload', ['date_id' => $date->id]); ?>
    </div>
</div>
