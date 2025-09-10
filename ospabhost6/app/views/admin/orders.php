<div class="container">
    <h1>Панель оператора</h1>
    <h2>Заказы на проверку</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Нет заказов для проверки</div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <h3>Заказ #<?= $order['id'] ?></h3>
                        <span class="status"><?= $order['status'] ?></span>
                    </div>
                    
                    <div class="order-info">
                        <p><strong>Пользователь:</strong> <?= $order['user_name'] ?> (<?= $order['email'] ?>)</p>
                        <p><strong>Тип:</strong> <?= $order['type'] === 'invoice' ? 'Пополнение счета' : 'Услуга' ?></p>
                        <p><strong>Сумма:</strong> <?= $order['amount'] ?> руб.</p>
                        <p><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>

                    <?php if ($order['screenshot_path']): ?>
                        <div class="cheque-preview">
                            <h4>Чек:</h4>
                            <img src="/uploads/cheques/<?= $order['screenshot_path'] ?>" 
                                 alt="Чек" style="max-width: 300px; border-radius: 5px;">
                        </div>
                    <?php endif; ?>

                    <div class="order-actions">
                        <form action="/admin/approve" method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success">Подтвердить</button>
                        </form>
                        
                        <form action="/admin/approve" method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger">Отклонить</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.order-card {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
    border: 1px solid #dee2e6;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.status {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.cheque-preview {
    margin: 1rem 0;
    padding: 1rem;
    background: #fff;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.order-actions {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}
</style>