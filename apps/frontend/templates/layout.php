<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <?php include_title() ?>
        <link rel="shortcut icon" href="/favicon.ico" />
        <?php include_stylesheets() ?>
        <?php include_javascripts() ?>
    </head>
    <body>
        <div id="login">
            <h1>zalogowany jako: <?php echo $sf_user->getLogin(); ?></h1>
            <form method="POST" action="<?php echo url_for('default/login'); ?>">
                <table>
                    <?php
                    $form = new BaseForm();
                    $form->setWidget('login', new sfWidgetFormPropelChoice(array('model' => 'User', 'add_empty' => 'wybierz...')));
                    echo $form;
                    ?>
                    <tr><td><input type="submit" value="OK" /></td></tr>
                </table>
            </form>
        </div>

<?php echo $sf_content ?>
    </body>
</html>
