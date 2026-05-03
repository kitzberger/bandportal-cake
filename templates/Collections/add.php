<div class="collections form large-9 medium-8 columns content">
    <?= $this->Form->create($collection, [
        'url' => \Cake\Routing\Router::url(['controller' => 'Collections', 'action' => 'add'])
    ]) ?>
    <fieldset>
        <legend><?= __('Add Collection') ?></legend>
        <?php
            echo $this->Form->control('title', ['autofocus' => 1]);
            echo '<div data-alert class="alert-box info">' . __('Select files and songs for this collection right below or (mass) upload them afterwards when visiting the collection.') . '</div>';
            if (count($files)) {
                echo $this->Form->control('files._ids', ['options' => $files]);
            }
            if (count($songs)) {
                echo $this->Form->control('songs._ids', ['options' => $songs]);
            }
        ?>
    </fieldset>
    <?php
        if ($currentUser['is_admin']) {
            ?>
    <fieldset>
        <legend><?= __('Admin fields') ?></legend>
        <?= $this->Form->control('user_id', ['options' => $users, 'empty' => true, 'default' => $currentUser['id']]) ?>
    </fieldset>
    <?php
        } else {
            echo $this->Form->control('user_id', ['type' => 'hidden', 'default' => $currentUser['id']]);
        }
    ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
