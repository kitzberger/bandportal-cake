<?php

if (isset($currentUserShares)) {
    foreach ($currentUserShares as $share) {
        echo '<li>';
        if ($share->hasValue('date')) {
            echo $this->Html->link(
                '<i class="fi-calendar"></i> ' . $share->date->title,
                ['controller' => 'Dates', 'action' => 'view', $share->date->id],
                ['escape' => false, 'class' => $controller=='Dates' && isset($date) && $date->id == $share->date->id ? 'active' : '']
            );
        };
        if ($share->hasValue('idea')) {
            echo $this->Html->link(
                '<i class="fi-lightbulb"></i> ' . $share->idea->title,
                ['controller' => 'Ideas', 'action' => 'view', $share->idea->id],
                ['escape' => false, 'class' => $controller=='Ideas' && isset($idea) && $idea->id == $share->idea->id ? 'active' : '']
            );
        }
        if ($share->hasValue('song')) {
            echo $this->Html->link(
                '<i class="fi-music"></i> ' . $share->song->title,
                ['controller' => 'Songs', 'action' => 'view', $share->song->id],
                ['escape' => false, 'class' => $controller=='Songs' && isset($song) && $song->id == $share->song->id ? 'active' : '']
            );
        }
        if ($share->hasValue('collection')) {
            echo $this->Html->link(
                '<i class="fi-list-thumbnails"></i> ' . $share->collection->title,
                ['controller' => 'Collections', 'action' => 'view', $share->collection->id],
                ['escape' => false, 'class' => $controller=='Collections' && isset($collection) && $collection->id == $share->collection->id ? 'active' : '']
            );
        }
        if ($share->hasValue('file')) {
            echo $this->Html->link(
                '<i class="fi-download"></i> ' . $share->file->title,
                ['controller' => 'Files', 'action' => 'view', $share->file->id],
                ['escape' => false, 'class' => $controller=='Files' && isset($file) && $file->id == $share->file->id ? 'active' : '']
            );
        }
        echo '</li>';
    }
} else {
    echo '<li>' . __('No shares available.') . '</li>';
}
