<div class="container">
    <h1>Оплата заказа #<?= $order['id'] ?></h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="payment-info">
        <div class="order-summary">
            <h3>Информация о заказе</h3>
            <p><strong>Тип:</strong> <?= $order['type'] === 'invoice' ? 'Пополнение счета' : 'Услуга' ?></p>
            <p><strong>Сумма:</strong> <?= $order['amount'] ?> руб.</p>
            <p><strong>Статус:</strong> <?= $this->getStatusText($order['status']) ?></p>
        </div>

        <div class="payment-methods">
            <h3>Способы оплаты</h3>
            
            <div class="payment-method">
                <h4>💳 Перевод по СБП</h4>
                <div class="qr-code">
                    <!-- Генерация QR-кода для СБП -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('СБП:2200XXXXXXXXXXXX') ?>" alt="QR-код СБП">
                </div>
                <p>Отсканируйте QR-код для перевода</p>
            </div>

            <div class="payment-method">
                <h4>🏦 Перевод на карту</h4>
                <div class="card-info">
                    <p><strong>Номер карты:</strong> 2200 XXXX XXXX XXXX</p>
                    <p><strong>Получатель:</strong> Иванов И.И.</p>
                    <p><strong>Банк:</strong> Тинькофф</p>
                </div>
            </div>
        </div>

        <div class="cheque-upload">
            <h3>Подтверждение оплаты</h3>
            <form action="/payment/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                
                <div class="form-group">
                    <label for="cheque">Прикрепите скриншот чека:</label>
                    <input type="file" id="cheque" name="cheque" accept="image/*" required>
                    <small>Формат: JPG, PNG, GIF. Максимальный размер: 5MB</small>
                </div>

                <button type="submit" class="btn btn-primary">Подтвердить оплату</button>
            </form>
        </div>
    </div>
</div>

<style>
.payment-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.payment-method {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.qr-code {
    text-align: center;
    margin: 1rem 0;
}

.cheque-upload {
    grid-column: 1 / -1;
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
}
</style>

<?php
// Вспомогательная функция для отображения статуса
function getStatusText($status) {
    $statuses = [
        'pending' => 'Ожидает оплаты',
        'awaiting_verification' => 'Ожидает проверки',
        'completed' => 'Завершен',
        'rejected' => 'Отклонен'
    ];
    return $statuses[$status] ?? $status;
}
?>