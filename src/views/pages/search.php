<?=$render('header', ['loggedUser'=> $loggedUser]);?>

<section class="container main">
    <?= $render('sidebar', ['menuActive' => 'search']); ?>

    <section class="feed mt-10">

        <div class="row">
            <div class="column pr-5">

                <h1>Voce pesquisou por: <?=$searchTerm?></h1>

                <?php foreach($users as $user):?>
                    <div class="friend-icon search">
                        <a href="<?=$base;?>/perfil/<?=$user->id?>">
                            <div class="friend-icon-avatar search">
                                <img src="<?=$base;?>/media/avatars/<?=$user->avatar?>" />
                            </div>
                            <div class="friend-icon-name search">
                                <?=$user->name?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="column side pl-5">
                <?=$render('right-bar');?>
            </div>

        </div>

    </section>

</section>
<?=$render('footer');?>