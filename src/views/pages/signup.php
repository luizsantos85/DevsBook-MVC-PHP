<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Cadastro - Devsbook</title>
  <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />
  <link rel="stylesheet" href="<?= $base; ?>/assets/css/login.css" />
</head>

<body>
  <header>
    <div class="container">
      <a href="<?= $base; ?>/"><img src="<?= $base; ?>/assets/images/devsbook_logo.png" /></a>
    </div>
  </header>
  <section class="container main">
    <form method="POST" action="<?= $base; ?>/cadastro">

      <?php if (!empty($flash)) : ?>
        <div class="error" id="flash"><?= $flash; ?> </div>
      <?php endif; ?>

      <input placeholder="Nome" class="input" type="text" name="name" />

      <input placeholder="E-mail" class="input" type="email" name="email" />

      <input placeholder="Senha" class="input" type="password" name="password" />

      <input placeholder="Data de nascimento" class="input" type="text" name="birthdate" id="birthdate" />

      <input class="button" type="submit" value="Fazer cadastro" />

      <a href="<?= $base; ?>/login">Já possui conta? Faça o login.</a>
    </form>
  </section>


  <script src="https://unpkg.com/imask"></script>
  <script>
    function removeMensagem() {
      const el = document.getElementById('flash');
      if (el) {
        setTimeout(() => {
          el.parentNode.removeChild(el);
        }, 3000);
      }
    }
    removeMensagem();

    IMask(
      document.getElementById('birthdate'), {
        mask: '00/00/0000'
      }
    )
  </script>

</body>

</html>