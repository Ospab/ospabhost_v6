<section class="container">
	<h1>Профиль</h1>
	<p>Здесь вы можете изменить свои данные, пароль и настройки безопасности.</p>
	<form action="/dashboard/profile" method="post">
		<label for="name">Имя:</label>
		<input type="text" id="name" name="name" value="<?= e($user['name'] ?? '') ?>" required>
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" value="<?= e($user['email'] ?? '') ?>" required>
		<button type="submit" class="btn btn-primary">Сохранить</button>
	</form>
</section>
