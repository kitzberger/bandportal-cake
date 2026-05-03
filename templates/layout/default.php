<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
    <?= $this->Html->css('dropzone.css') ?>
    <?= $this->Html->css('bandcake.css') ?>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.9.1/fullcalendar.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css" />

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/foundicons/3.0.0/foundation-icons.css" />
</head>
<body>
    <nav class="top-bar expanded" data-topbar role="navigation">
        <ul class="title-area large-3 medium-4 columns">
            <li class="name">
                <h1><?= $this->Html->link(__('Bandportal 2.0'), ['controller' => 'Misc', 'action' => 'dashboard']) ?></a></h1>
            </li>
        </ul>
        <div class="top-bar-section hide-for-print">
            <ul class="right">
                <?php if ($currentUser) { ?>
                <li>
                    <a href="#">
                        <?= __('Logged in as') . ' ' . $currentUser['username'] ?>
                        <?= ($currentUser['is_admin'] ? '<b style="color:#FF7473">(admin!)</b>' : '') ?>
                    </a>
                </li>
                <li><?= $this->Html->link(
                    '<i class="fi-x-circle show-for-small-only"></i> <span class="hide-for-small-only">' . __('Logout') . '</span>',
                    ['controller' => 'Users', 'action' => 'logout'],
                    ['escape' => false]) ?>
                </li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <?= $this->Flash->render() ?>
    <div id="main-container" class="container clearfix">
        <?php if ($currentUser) { ?>
        <nav class="large-3 medium-4 columns hide-for-print" id="actions-sidebar">
            <ul class="side-nav">
                <?php
                    if ($currentUser['is_passive']) {
                        echo '<li class="heading heading-menu">' . __('Shares') . '</li>';
                        echo $this->element('Navigation/shares');
                    }
                    if ($currentUser['is_active'] || $currentUser['is_admin']) {
                        echo '<li class="heading heading-menu">' . __('Menu') . '</li>';
                        echo $this->element('Navigation/default');
                    }
                    if ($currentUser['is_admin']) {
                        echo '<li class="heading heading-admin">' . __('Admin') . ' <i class="fi-wrench"></i></li>';
                        echo $this->element('Navigation/admin');
                    }
                ?>
            </ul>
        </nav>
        <?php } ?>
        <?= $this->fetch('content') ?>
    </div>
    <footer>
        <div id="audioplayer" style="display: none" data-file-id="" data-file-unlocked="0">
            <div class="topbar">
                <span class="title"></span>
                <a class="url edit"><i class="fi-pencil"></i></a>
                <a class="url download"><i class="fi-download"></i></a>
            </div>
            <div class="waveform"></div>
            <div class="timeline"></div>
            <div class="toolbar">
                <input type="checkbox" value="1" id="loop"><label for="loop">Loop?</label>
                <a class="button success" data-action="waveform-playPause">Play/pause</a>
                <span class="regions"></span>
                <a class="button warning" data-action="waveform-edit">
                    <span class="button-edit">Edit</span>
                    <span class="button-save">Save</span>
                </a>
                <a class="button alert" data-action="waveform-hide">Close</a>
            </div>
        </div>
    </footer>
    <script>
        // Workaround disabling pinch-zoom on iOS: https://stackoverflow.com/questions/4389932/how-do-you-disable-viewport-zooming-on-mobile-safari
        document.addEventListener('gesturestart', function (e) { e.preventDefault(); });

        let csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;
        let urlFileEdit = '<?= $this->Url->build(['controller' => 'Files', 'action' => 'edit']) ?>';
    </script>
    <script src="//unpkg.com/wavesurfer.js@6.6.3/dist/wavesurfer.js"></script>
    <script src="//unpkg.com/wavesurfer.js@6.6.3/dist/plugin/wavesurfer.cursor.js"></script>
    <script src="//unpkg.com/wavesurfer.js@6.6.3/dist/plugin/wavesurfer.regions.js"></script>
    <script src="//unpkg.com/wavesurfer.js@6.6.3/dist/plugin/wavesurfer.timeline.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <?= $this->Html->script('dropzone.js') ?>
    <?= $this->Html->script('foundation.js') ?>
    <?= $this->Html->script('foundation.dropdown.js') ?>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.9.1/fullcalendar.js"></script>
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.9.1/lang/de.js"></script>-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/locale/de.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>
    <?= $this->Html->script('bandcake.js') ?>
</body>
</html>
