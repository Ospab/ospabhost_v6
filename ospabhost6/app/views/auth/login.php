<section class="container">
	<h1>Вход в личный кабинет</h1>
	<?php if (!empty($error)): ?>
		<div class="alert alert-danger"><?= $error ?></div>
	<?php endif; ?>
	<form action="/auth/login" method="post">
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required>
		<label for="password">Пароль:</label>
		<input type="password" id="password" name="password" required>
		<button type="submit" class="btn btn-primary">Войти</button>
	</form>
	<p><a href="/auth/forgot-password">Забыли пароль?</a></p>
</section>
