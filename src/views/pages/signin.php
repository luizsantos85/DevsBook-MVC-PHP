<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Login - Devsbook</title>
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
    <form method="POST" action="<?= $base; ?>/login">

      <?php if (!empty($flash)) : ?>
        <div class="error" id="flash"><?= $flash; ?> </div>
      <?php endif; ?>

      <input placeholder="E-mail" class="input" type="email" name="email" />

      <input placeholder="Senha" class="input" type="password" name="password" />

      <input class="button" type="submit" value="Acessar o sistema" />

      <a href="<?= $base; ?>/cadastro">Ainda não tem conta? Cadastre-se!</a>
      <a href="<?= $base; ?>/resetPass">Esqueci a senha!</a>
    </form>
  </section>

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
  </script>

</body>
</html>