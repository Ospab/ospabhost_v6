<div class="dashboard-container">
    <h1>Мои серверы</h1>
    
    <?php if (isset($_GET['success']) && $_GET['success'] === 'service_created'): ?>
        <div class="alert alert-success">Сервер успешно создан!</div>
    <?php endif; ?>

    <a href="/dashboard/create-service" class="btn btn-primary">Создать новый сервер</a>

    <div class="services-grid">
        <?php if (empty($services)): ?>
            <p>У вас пока нет серверов.</p>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <h3>Сервер #<?= $service['id'] ?></h3>
                    <p>Тип: <?= $service['type'] ?></p>
                    <p>Статус: <?= $service['status'] ?></p>
                    <p>Создан: <?= date('d.m.Y H:i', strtotime($service['created_at'])) ?></p>
                    
                    <?php if (!empty($service['status_info'])): ?>
                        <p>Состояние: <?= $service['status_info']['status'] ?? 'unknown' ?></p>
                    <?php endif; ?>

                    <div class="service-actions">
                        <button class="btn btn-small">Управление</button>
                        <button class="btn btn-small btn-danger">Остановить</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>