<div class="songs view large-9 medium-8 columns content">
    <h3>
        <?= h($song->title) ?>
        <?php $this->assign('title', $song->title); ?>
        <small>
            <?= $this->Html->link(__('Show'), ['controller' => 'Songs', 'action' => 'view', $song->id]) ?>
        </small>
        <?php if ($song['text']) { ?>
        <small class="right">
            <?php
$iconCapo = '<svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
  <!-- fretboard -->
  <line x1="16" y1="8" x2="16" y2="56"/>
  <line x1="28" y1="8" x2="28" y2="56"/>
  <line x1="40" y1="8" x2="40" y2="56"/>
  <line x1="52" y1="8" x2="52" y2="56"/>

  <!-- capo -->
  <rect x="12" y="24" width="44" height="8" rx="3" fill="currentColor"/>
</svg>';

$iconCapoOff = '<svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
  <!-- fretboard -->
  <line x1="16" y1="8" x2="16" y2="56"/>
  <line x1="28" y1="8" x2="28" y2="56"/>
  <line x1="40" y1="8" x2="40" y2="56"/>
  <line x1="52" y1="8" x2="52" y2="56"/>

  <!-- capo ghost -->
  <rect x="12" y="24" width="44" height="8" rx="3"/>

  <!-- slash -->
  <line x1="10" y1="54" x2="54" y2="10"/>
</svg>';

                $originalCapo = $this->Chord->determineCapo($song['text']);
                if ($transposeBy === 'off') {
                    $left = $originalCapo - 1;
                    $right = $originalCapo + 1;
                } else {
                    $left = $transposeBy - 1;
                    $right = $transposeBy + 1;
                }

                echo $this->Html->link(
                    '<i class="fi-arrow-left"></i>',
                    ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => $left]],
                    ['escape' => false, 'class' => 'button small', 'title' => __('Capo left')]
                ) . ' ';
                if ($originalCapo !== 0) {
                    echo $this->Html->link(
                        $iconCapo,
                        ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => 0]],
                        ['escape' => false, 'class' => 'button small with-svg' . ($transposeBy == 0 ? ' active' : ''), 'title' => __('Capo on')]
                    ) . ' ';
                    echo $this->Html->link(
                        $iconCapoOff,
                        ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => 'off']],
                        ['escape' => false, 'class' => 'button small with-svg' . ($transposeBy === 'off' ? ' active' : ''), 'title' => __('Capo off')]
                    ) . ' ';
                }
                echo $this->Html->link(
                    '<i class="fi-arrow-right"></i>',
                    ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => $right]],
                    ['escape' => false, 'class' => 'button small', 'title' => __('Capo right')]
                ) . ' ';
                echo $this->Html->link(
                    '<i class="fi-page-pdf"></i> ' . __('PDF'),
                    ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => $transposeBy, 'mode' => 'full', 'zoom' => '1.0'], '_ext' => 'pdf'],
                    ['escape' => false, 'class' => 'button small success']
                ) . ' ';
                echo $this->Html->link(
                    '<i class="fi-page-pdf"></i> ' . __('PDF (Text)'),
                    ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => $transposeBy, 'mode' => 'text', 'zoom' => '1.0'], '_ext' => 'pdf'],
                    ['escape' => false, 'class' => 'button small success']
                ) . ' ';
                echo $this->Html->link(
                    '<i class="fi-page-pdf"></i> ' . __('PDF (Chords)'),
                    ['controller' => 'Songs', 'action' => 'display', $song->id, '?' => ['transposeBy' => $transposeBy, 'mode' => 'chords', 'zoom' => '1.0'], '_ext' => 'pdf'],
                    ['escape' => false, 'class' => 'button small success']
                );
            ?>
        </small>
        <?php }?>
    </h3>

    <?php if ($song['text']) { ?>
        <div class="markdown mode-<?= $mode ?>"><?= $this->Markdown->transform($this->Chord->render($song['text'], ['transposeBy' => $transposeBy, 'mode' => $mode])) ?></div>
    <?php } ?>
</div>
