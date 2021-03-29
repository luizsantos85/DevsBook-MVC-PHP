<?= $render('header', ['loggedUser' => $loggedUser]); ?>

<section class="container main">
  <?= $render('sidebar', ['activeMenu' => 'config']); ?>

  <section class="feed mt-10">
    <div class="row">
      <div class="column pr-5">
        <h2>Configurações</h2>

        <form action="">
          <div>
            <label for="avatar">
              Novo avatar:
            </label>
            <input type="file" id="avatar">
          </div>
          <div>
            <label for="capa">
              Novo capa:
            </label>
            <input type="file" id="capa">
          </div>

          <hr>

          <div>
            <label for="name">
              Nome completo:
            </label>
            <input type="text" id="name" value="<?= $user->name; ?>">
          </div>

          <div>
            <label for="birthdate">
              Data de nascimento:
            </label>
            <input type="text" id="birthdate" value="<?= $user->birthdate; ?>">
          </div>

          <div>
            <label for="email">
              E-mail:
            </label>
            <input type="text" id="email" value="<?= $user->email; ?>">
          </div>

          <div>
            <label for="city">
              Cidade:
            </label>
            <input type="text" id="city" value="<?= $user->city; ?>">
          </div>

          <div>
            <label for="work">
              Trabalho:
            </label>
            <input type="text" id="work" value="<?= $user->work; ?>">
          </div>

          <hr>

          <div>
            <label for="password">
              Senha:
            </label>
            <input type="text" id="password" value="">
          </div>
          <div>
            <label for="confirmPass">
              Confirmar senha:
            </label>
            <input type="text" id="confirmPass" >
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