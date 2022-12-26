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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/foundation-datepicker/1.5.6/css/foundation-datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.min.css" />

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
        <div class="top-bar-section">
            <ul class="right">
                <?php if ($currentUser) { ?>
                <li>
                    <a href="#">
                        <?= __('Logged in as') . ' ' . $currentUser['username'] ?>
                        <?= ($currentUser['is_admin'] ? '<b style="color:#FF7473">(admin!)</b>' : '') ?>
                    </a>
                </li>
                <li><?= $this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout']) ?></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?php if ($currentUser) { ?>
        <nav class="large-3 medium-4 columns" id="actions-sidebar">
            <ul class="side-nav">
                <?php
                    echo '<li class="heading heading-menu">' . __('Menu') . '</li>';
                    if ($currentUser['is_active']) {
                        echo $this->element('Navigation/default');
                    } else {
                        echo $this->element('Navigation/shares');
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
    </footer>
    <script>
        // Workaround disabling pinch-zoom on iOS: https://stackoverflow.com/questions/4389932/how-do-you-disable-viewport-zooming-on-mobile-safari
        document.addEventListener('gesturestart', function (e) { e.preventDefault(); });
    </script>
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
    <script src="//cdnjs.cloudflare.com/ajax/libs/foundation-datepicker/1.5.6/js/foundation-datepicker.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/foundation-datepicker/1.5.6/js/locales/foundation-datepicker.de.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <?= $this->Html->script('bandcake.js') ?>
</body>
</html>