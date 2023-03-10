<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Add User') ?></legend>
        <?php
            echo $this->Form->control('username', ['autofocus' => 1, 'autocomplete' => 'off', 'data-lpignore' => 'true']);
            echo $this->Form->control('email', ['autocomplete' => 'off', 'data-lpignore' => 'true']);
            echo $this->Form->control('password', ['autocomplete' => 'new-password', 'data-lpignore' => 'true']);
            echo $this->Form->control('is_admin');
            echo $this->Form->control('is_active');
            echo $this->Form->control('is_passive');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
