<?php
    $editField = $this->request->getQuery('field');
?>
<div class="songs form large-9 medium-8 columns content">
    <?= $this->Form->create($song) ?>
    <fieldset>
        <legend><?= __('Edit Song') ?></legend>
        <?php
            $this->Form->setTemplates(['formGroup' => '{{label}}{{hint}}{{input}}']);

            if (!$editField) {
                echo $this->Form->control('title');
                echo $this->Form->control('artist');
                if ($currentUser['is_admin']) {
                    echo $this->Form->control('is_pseudo');
                    echo $this->Form->control('zoom_full', ['step' => 0.01]);
                    echo $this->Form->control('zoom_text', ['step' => 0.01]);
                    echo $this->Form->control('zoom_chords', ['step' => 0.01]);
                    if ($githubEnabled) {
                        echo $this->Form->control('url', ['label' => __('URL') . ' (' . \Cake\Core\Configure::read('Github.repository_url') . ')']);
                    }
                }
                if (empty($song->url)) {
                    echo $this->Form->control('text', [
                        'autofocus' => 1,
                        'templateVars' => [
                            'hint' => '<p class="hint">Supports <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank">markdown</a> syntax.</p>',
                        ]
                    ]);
                }
            }

            // TODO actually interpret value of $editField
            echo $this->Form->control('instructions', ['type' => 'textarea']);
        ?>
    </fieldset>
    <?php if (!$editField): ?>
    <?= $this->element('Forms/UserSelect') ?>
    <?php endif; ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
