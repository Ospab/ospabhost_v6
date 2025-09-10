<?php
class HomeController {
    public function index() {
        $data = ['title' => 'Главная - OSPAB Host'];
        $this->render('home', $data);
    }

    private function render($view, $data = []) {
        extract($data);
        require_once "../app/views/layouts/header.php";
        require_once "../app/views/$view.php";
        require_once "../app/views/layouts/footer.php";
    }
}
?>