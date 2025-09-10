<section class="container">
	<h1>Регистрация</h1>
	<?php if (!empty($error)): ?>
		<div class="alert alert-danger"><?= $error ?></div>
	<?php endif; ?>
	<form action="/auth/register" method="post">
		<label for="name">Имя:</label>
		<input type="text" id="name" name="name" required>
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required>
		<label for="password">Пароль:</label>
		<input type="password" id="password" name="password" required>
		<label for="confirm_password">Повторите пароль:</label>
		<input type="password" id="confirm_password" name="confirm_password" required>
		<button type="submit" class="btn btn-primary">Зарегистрироваться</button>
	</form>
</section>
