<?= $render('header', ['loggedUser' => $loggedUser]); ?>

<section class="container main">
  <?= $render('sidebar', ['activeMenu' => 'config']); ?>

  <section class="feed mt-10">
    <div class="row">
      <div class="column pr-5">
        <h2>Configurações</h2>

        <form action="<?= $base; ?>/config" method="POST" class="config" enctype="multipart/form-data">
          <?php if (!empty($flash)) : ?>
            <div class="error" id="flash"><?= $flash; ?> </div>
          <?php endif; ?>
          <div >
            <label for="avatar">
              Novo avatar:
            </label>
            <input type="file" id="avatar" name="avatar">
          </div>
          <div>
            <label for="capa">
              Nova capa:
            </label>
            <input type="file" id="capa" name="cover">
          </div>

          <hr>

          <div>
            <label for="name">
              Nome completo:
            </label>
            <input type="text" id="name" value="<?= $user->name; ?>" name="name">
          </div>

          <div>
            <label for="birthdate">
              Data de nascimento:
            </label>
            <input type="text" id="birthdate" value="<?= date('d/m/Y', strtotime($user->birthdate)); ?>" name="birthdate">
          </div>

          <div>
            <label for="email">
              E-mail:
            </label>
            <input type="text" id="email" value="<?= $user->email; ?>" name="email">
          </div>

          <div>
            <label for="city">
              Cidade:
            </label>
            <input type="text" id="city" value="<?= $user->city; ?>" name="city">
          </div>

          <div>
            <label for="work">
              Trabalho:
            </label>
            <input type="text" id="work" value="<?= $user->work; ?>" name="work">
          </div>

          <hr>

          <div>
            <label for="password">
              Senha:
            </label>
            <input type="password" id="password" name="password">
          </div>
          <div>
            <label for="passwordConfirm">
              Confirmar senha:
            </label>
            <input type="password" id="passwordConfirm" name="passwordConfirm">
          </div>

          <button class="button">Salvar</button>
        </form>
      </div>

      <div class="column side pl-5">
        <?= $render('right-side'); ?>
      </div>
    </div>


  </section>
</section>




<?= $render('footer'); ?>