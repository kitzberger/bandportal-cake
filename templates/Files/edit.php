<div class="files form large-9 medium-8 columns content">
    <?= $this->Form->create($file) ?>
    <fieldset>
        <legend><?= __('Edit File') ?></legend>
        <?php
            $this->Form->setTemplates(['formGroup' => '{{label}}{{hint}}{{input}}']);

            echo $this->Form->control('title');
            echo $this->Form->control('file', ['readonly']);
            echo $this->Form->control('collections._ids', ['options' => $collections]);
            echo $this->Form->control('date_id', ['options' => $dates, 'empty' => true]);
            echo $this->Form->control('idea_id', ['options' => $ideas, 'empty' => true]);
            if ($file->isAudio()) {
                echo $this->Form->control('regions', [
                    'placeholder' => '[' . PHP_EOL . '  {"id": 123, "start": 10.0, "end": 20.0, "title": "Verse 1"},' . PHP_EOL . '  {"id": 124, "start": 30.0, "end": 50.0, "title": "Chorus"}' . PHP_EOL . ']',
                    'templateVars' => [
                        'hint' => '<p class="hint">Experimental feature! Needs to be a valid JSON string. Click <a href="https://wavesurfer-js.org/plugins/regions.html" target="_blank">here</a> for details.</p>',
                    ],
                ]);
                echo $this->Form->control(
                    'song_id',
                    [
                        'options' => $songs,
                        'empty' => true,
                        'default' => (isset($this->request->query['song_id']) ? $this->request->query['song_id'] : null)
                    ]
                );
                echo $this->Form->control(
                    'song_version_id',
                    [
                        'options' => $songsVersions,
                        'empty' => true,
                        'default' => (isset($this->request->query['song_version_id']) ? $this->request->query['song_version_id'] : null)
                    ]
                );
                echo $this->Form->control('is_public', ['type' => 'checkbox', 'label' => 'Set as reference file for this song/version?']);
            }
        ?>
    </fieldset>
    <?= $this->element('Forms/UserSelect') ?>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
