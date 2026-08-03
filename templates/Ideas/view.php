<div class="ideas view large-9 medium-8 columns content">
    <h3>
        <?= h($idea->title) ?>
        <?php $this->assign('title', $idea->title); ?>
        <small>
            <?= $this->Html->link('<i class="fi-pencil"></i> ' . __('Edit'), ['action' => 'edit', $idea->id], ['escape' => false]) ?>
            <?php
                if ($currentUser['is_active'] == 1 && $currentUser['id'] == $idea->user_id) {
                    echo $this->Form->postLink(
                        '<i class="fi-trash"></i> ' . __('Delete'),
                        ['action' => 'delete', $idea->id],
                        ['confirm' => __('Are you sure you want to delete "{0}"?', $idea->title), 'escape' => false]
                    );
                }
            ?>
        </small>
        <small class="right">
            <?php
                echo $this->element('Button/ShareLink', ['passiveUsers' => $passiveUsers, 'controller' => 'Ideas', 'record' => $idea]);
            ?>
        </small>
    </h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $idea->hasValue('user') ? $this->element('username', ['user' => $idea->user]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($idea->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($idea->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Shared with') ?></th>
            <td><?= $this->element('shares', ['shares' => $idea->shares]) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <?= $this->element('text', ['text' => $idea->text, 'markdown' => true]) ?>
    </div>
    <div class="related">
        <h4><?= __('Votes') ?></h4>
        <table>
            <tr>
                <th><?= __('Active') ?></th>
                <th><?= __('Passive') ?></th>
            </tr>
            <tr>
                <td>
                    <?= $this->element('votes', ['record' => $idea, 'users' => $users]) ?>
                </td>
                <td>
                    <?= $this->element('votes', ['record' => $idea, 'users' => $passiveUsers]) ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="related">
        <h4><?= __('Comments') ?></h4>
        <?= $this->element('Comments/list', ['comments' => $idea->comments]); ?>
        <?= $this->element('Comments/new', ['comment' => null, 'idea_id' => $idea->id]); ?>
    </div>
    <div class="related">
        <h4><?= __('Files') ?></h4>
        <?= $this->element('Files/list', ['files' => $idea->files]); ?>
        <?= $this->element('Files/upload', ['idea_id' => $idea->id]); ?>
    </div>
</div>
