<?php
require_once 'includes/functions.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
header('Location: ' . (($_SESSION['user_role'] ?? '') === 'admin' ? 'admin_dashboard.php' : 'dashboard.php'));
exit;
