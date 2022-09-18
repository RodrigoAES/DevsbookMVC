<?=$render('header', ['loggedUser'=> $loggedUser]);?>

<section class="container main">
    <?= $render('sidebar', ['menuActive' => 'search']); ?>

    <section class="feed mt-10">
        <h1 class="titleConfig">Configurações</h1>
        <div class="row">
            <div class="column pr-5">
                <?php if($flash != ''): ?>
                    <div class="flash"><?=$flash;?></div>
                <?php endif;?>
                <form class="configUpdate" method="POST" action="<?=$base;?>/config"  enctype="multipart/form-data">
                    <label>
                        Novo Avatar:</br></br>
                        <input type="file" name="avatar" accept="image/png, image/jpeg" /></br></br>
                        <img src="<?=$base;?>/media/avatars/<?=$user->avatar;?>"  class="avatar"/>
                    </label></br></br>
                    <label>
                        Novo Capa:</br></br>
                        <input type="file" name="cover" accept="image/png, image/jpeg" /></br></br>
                        <img src="<?=$base;?>/media/covers/<?=$user->cover;?>" class="cover"/>
                    </label></br></br>
                    <hr/></br>
                    <label>
                        Nome Completo:</br>
                        <input type="text" name="name" value="<?=$user->name;?>" />
                    </label></br></br>
                    <label>
                        Data de Nascimento:</br>
                        <input type="text" name="birthdate" value="<?=$birthdate;?>" id="birthdate"/>
                    </label></br></br>
                    <label>
                        E-mail:</br>
                        <input type="email" name="email" value="<?=$user->email;?>"/>
                    </label></br></br>
                    <label>
                        Cidade:</br>
                        <input type="text" name="city" value="<?=$user->city;?>" />
                    </label></br></br>
                    <label>
                        Trabalho:</br>
                        <input type="text" name="work" value="<?=$user->work;?>" />
                    </label></br></br>
                    <label>
                        Nova Senha:</br>
                        <input type="password" name="newPassword" />
                    </label></br></br>
                    <label>
                        Confirmação Nova Senha:</br>
                        <input type="password" name="newPasswordConfirm" />
                    </label></br></br></br></br></br>
                    <label>
                        Senha:</br>
                        <input type="password" name="password" />
                    </label></br></br>

                    <input class="button" type="submit" />
                </form>
                
            </div>

        </div>

    </section>

</section>
<script src="https://unpkg.com/imask"></script>
<script>
IMask(
    document.getElementById('birthdate'),
    {mask: '00/00/0000'}
)  
</script>
<?=$render('footer');?>