<div class="songs form large-9 medium-8 columns content">
    <?= $this->Form->create($song) ?>
    <fieldset>
        <legend><?= __('Add Song') ?></legend>
        <?php
            $this->Form->setTemplates(['formGroup' => '{{label}}{{hint}}{{input}}']);

            echo $this->Form->control('title', ['autofocus' => 1]);
            echo $this->Form->control('artist');
            if ($currentUser['is_admin']) {
                echo $this->Form->control('is_pseudo');
                if ($githubEnabled) {
                    $hint = \Cake\Core\Configure::read('Github.repository_url');
                    echo $this->Form->control('url', [
                        'label' => __('URL') . ' <span class="show-for-medium">(' . $hint . ')</span>',
                        'escape' => false
                    ]);
                }
            }
            if (empty($song->url)) {
                echo $this->Form->control('text', [
                    'templateVars' => [
                        'hint' => '<p class="hint">Supports <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank">markdown</a> syntax.</p>',
                    ]
                ]);
            }
            echo $this->Form->control('instructions', ['type' => 'textarea']);
        ?>
    </fieldset>
    <?= $this->element('Forms/UserSelect') ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
