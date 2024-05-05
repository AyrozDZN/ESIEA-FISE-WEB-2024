<?php
// Controller.php

require_once 'model.php';
require_once 'security.php';
require_once 'service.php';

class Controller {
    public function handleRequest() {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_GET['action'])) {
                $action = $_GET['action'];

                switch ($action) {
                    case 'register':
                        Service::handleRegisterAction();
                        break;
                    case 'login':
                        Service::handleLoginAction();
                        break;
                    case 'dashboard':
                        include_once 'templates/dashboard.php';
                        break;
                    case 'update':
                        Service::handleUpdateAction();
                        break;
                    case 'close':
                        Service::handleCloseAction();
                        break;
                    case 'logout':
                        Service::handleLogoutAction();
                        break;
                    default:
                        include_once 'templates/home.php';
                        break;
                }
            } else {
                include_once 'templates/home.php';
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            require_once 'templates/error.php';
        }
    }
}
?>